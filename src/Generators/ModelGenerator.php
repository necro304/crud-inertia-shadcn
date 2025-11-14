<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Generators;

use Isaac\CrudGenerator\Support\NamingConverter;
use Isaac\CrudGenerator\Support\StubRenderer;
use Illuminate\Support\Facades\File;

class ModelGenerator
{
    public function __construct(
        private readonly StubRenderer $renderer = new StubRenderer()
    ) {
    }

    /**
     * Generate Model file
     *
     * @param  string  $resourceName  PascalCase resource name
     * @param  array<array{name: string, type: string, modifiers: array<string>}>  $fields
     * @param  array<string, mixed>  $options
     * @return string  Path to generated file
     */
    public function generate(string $resourceName, array $fields, array $options = []): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/model.stub';
        $namespace = config('crud-generator.model_namespace', 'App\Models');

        $tokens = [
            'NAMESPACE' => $namespace,
            'CLASS' => $resourceName,
            'TABLE_PROPERTY' => $this->getTableProperty($resourceName, $options),
            'FILLABLE' => $this->getFillableFields($fields),
            'CASTS' => $this->getCasts($fields),
            'SOFT_DELETES_IMPORT' => $this->getSoftDeletesImport($options),
            'SOFT_DELETES_TRAIT' => $this->getSoftDeletesTrait($options),
            'AUDITABLE_IMPORT' => $this->getAuditableImport($options),
            'AUDITABLE_TRAIT' => $this->getAuditableTrait($options),
            'RELATIONSHIPS' => $options['relationships'] ?? '',
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);

        $filePath = app_path("Models/{$resourceName}.php");
        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, $content);

        return $filePath;
    }

    private function getTableProperty(string $resourceName, array $options): string
    {
        if (isset($options['table'])) {
            return "protected \$table = '{$options['table']}';";
        }

        return '';
    }

    private function getFillableFields(array $fields): string
    {
        $fieldNames = array_map(fn ($field) => "'{$field['name']}'", $fields);

        return implode(",\n        ", $fieldNames);
    }

    private function getCasts(array $fields): string
    {
        $casts = [];

        foreach ($fields as $field) {
            $cast = $this->getCastForType($field['type']);
            if ($cast) {
                $casts[] = "'{$field['name']}' => '{$cast}'";
            }
        }

        return implode(",\n        ", $casts);
    }

    private function getCastForType(string $type): ?string
    {
        $typeMap = config('crud-generator.field_types', []);

        return $typeMap[$type]['cast'] ?? null;
    }

    private function getSoftDeletesImport(array $options): string
    {
        $useSoftDeletes = $options['soft_deletes'] ?? config('crud-generator.defaults.soft_deletes', true);

        return $useSoftDeletes ? "\nuse Illuminate\\Database\\Eloquent\\SoftDeletes;" : '';
    }

    private function getSoftDeletesTrait(array $options): string
    {
        $useSoftDeletes = $options['soft_deletes'] ?? config('crud-generator.defaults.soft_deletes', true);

        return $useSoftDeletes ? ', SoftDeletes' : '';
    }

    private function getAuditableImport(array $options): string
    {
        $useAuditing = $options['auditing'] ?? config('crud-generator.defaults.auditing', true);

        return $useAuditing ? "\nuse OwenIt\\Auditing\\Contracts\\Auditable;" : '';
    }

    private function getAuditableTrait(array $options): string
    {
        $useAuditing = $options['auditing'] ?? config('crud-generator.defaults.auditing', true);

        return $useAuditing ? ', \\OwenIt\\Auditing\\Auditable' : '';
    }
}
