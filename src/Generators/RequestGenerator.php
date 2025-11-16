<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn\Generators;

use Illuminate\Support\Facades\File;
use Necro304\CrudInertiaShadcn\Parsers\ValidationRuleBuilder;
use Necro304\CrudInertiaShadcn\Support\NamingConverter;
use Necro304\CrudInertiaShadcn\Support\StubRenderer;

class RequestGenerator
{
    public function __construct(
        private readonly StubRenderer $renderer = new StubRenderer,
        private readonly ValidationRuleBuilder $ruleBuilder = new ValidationRuleBuilder
    ) {}

    /**
     * Generate Store and Update Request files
     *
     * @param string $resourceName PascalCase resource name
     * @param array<array{name: string, type: string, modifiers: array<string>}> $fields
     *
     * @return array{store: string, update: string} Paths to generated files
     */
    public function generate(string $resourceName, array $fields): array
    {
        $namespace = config('crud-generator.request_namespace', 'App\Http\Requests');
        $tableName = NamingConverter::toTableName($resourceName);

        $storeFilePath = $this->generateStoreRequest($resourceName, $fields, $namespace, $tableName);
        $updateFilePath = $this->generateUpdateRequest($resourceName, $fields, $namespace, $tableName);

        return [
            'store' => $storeFilePath,
            'update' => $updateFilePath,
        ];
    }

    private function generateStoreRequest(string $resourceName, array $fields, string $namespace, string $tableName): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/store-request.stub';

        $tokens = [
            'NAMESPACE' => $namespace,
            'CLASS' => "Store{$resourceName}Request",
            'VALIDATION_RULES' => $this->buildValidationRules($fields, $tableName),
            'ATTRIBUTES' => $this->buildAttributes($fields),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);

        $filePath = app_path("Http/Requests/Store{$resourceName}Request.php");
        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, $content);

        return $filePath;
    }

    private function generateUpdateRequest(string $resourceName, array $fields, string $namespace, string $tableName): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/update-request.stub';

        $tokens = [
            'NAMESPACE' => $namespace,
            'CLASS' => "Update{$resourceName}Request",
            'VALIDATION_RULES_UPDATE' => $this->buildUpdateValidationRules($fields, $tableName),
            'ATTRIBUTES' => $this->buildAttributes($fields),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);

        $filePath = app_path("Http/Requests/Update{$resourceName}Request.php");
        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, $content);

        return $filePath;
    }

    private function buildValidationRules(array $fields, string $tableName): string
    {
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = $this->ruleBuilder->build($field['name'], $field['type'], $field['modifiers']);

            // Replace table_name placeholder with actual table name
            $fieldRules = array_map(
                fn ($rule) => str_replace('table_name', $tableName, $rule),
                $fieldRules
            );

            $rulesString = "'" . implode("', '", $fieldRules) . "'";
            $rules[] = "'{$field['name']}' => [{$rulesString}]";
        }

        return implode(",\n            ", $rules);
    }

    private function buildUpdateValidationRules(array $fields, string $tableName): string
    {
        $rules = [];

        foreach ($fields as $field) {
            $fieldRules = $this->ruleBuilder->build($field['name'], $field['type'], $field['modifiers']);

            // For unique rules, add ignore clause for updates
            $fieldRules = array_map(function ($rule) use ($tableName, $field) {
                if (str_starts_with($rule, 'unique:')) {
                    return "Rule::unique('{$tableName}', '{$field['name']}')->ignore(\$this->route('" . lcfirst($tableName) . "'))";
                }

                return "'{$rule}'";
            }, $fieldRules);

            $rulesString = implode(', ', $fieldRules);
            $rules[] = "'{$field['name']}' => [{$rulesString}]";
        }

        return implode(",\n            ", $rules);
    }

    private function buildAttributes(array $fields): string
    {
        $attributes = [];

        foreach ($fields as $field) {
            $humanName = ucwords(str_replace('_', ' ', $field['name']));
            $attributes[] = "'{$field['name']}' => '{$humanName}'";
        }

        return implode(",\n            ", $attributes);
    }
}
