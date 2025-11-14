<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Generators;

use Isaac\CrudGenerator\Support\NamingConverter;
use Isaac\CrudGenerator\Support\StubRenderer;
use Illuminate\Support\Facades\File;

class ControllerGenerator
{
    public function __construct(
        private readonly StubRenderer $renderer = new StubRenderer()
    ) {
    }

    /**
     * Generate Controller file
     *
     * @param  string  $resourceName  PascalCase resource name
     * @param  array<array{name: string, type: string, modifiers: array<string>}>  $fields
     * @param  array<string, mixed>  $options
     * @return string  Path to generated file
     */
    public function generate(string $resourceName, array $fields, array $options = []): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/controller.stub';
        $namespace = config('crud-generator.controller_namespace', 'App\Http\Controllers');
        $modelNamespace = config('crud-generator.model_namespace', 'App\Models');
        $requestNamespace = config('crud-generator.request_namespace', 'App\Http\Requests');
        $resourceNamespace = config('crud-generator.resource_namespace', 'App\Http\Resources');

        $tableName = NamingConverter::toTableName($resourceName);
        $routeName = NamingConverter::toRouteName($resourceName);
        $modelVariable = lcfirst($resourceName);
        $modelVariablePlural = NamingConverter::toPlural($modelVariable);
        $vueDirectory = NamingConverter::toPascalCase(NamingConverter::toPlural($resourceName));

        $tokens = [
            'NAMESPACE' => $namespace,
            'CLASS' => $resourceName . 'Controller',
            'MODEL_NAMESPACE' => $modelNamespace,
            'REQUEST_NAMESPACE' => $requestNamespace,
            'RESOURCE_NAMESPACE' => $resourceNamespace,
            'MODEL' => $resourceName,
            'MODEL_VARIABLE' => $modelVariable,
            'MODEL_VARIABLE_PLURAL' => $modelVariablePlural,
            'ROUTE_NAME' => $routeName,
            'VUE_DIRECTORY' => $vueDirectory,
            'EAGER_LOAD' => $this->getEagerLoad($options),
            'EAGER_LOAD_SHOW' => $this->getEagerLoadShow($modelVariable, $options),
            'EAGER_LOAD_EDIT' => $this->getEagerLoadEdit($modelVariable, $options),
            'ALLOWED_FILTERS' => $this->getAllowedFilters($fields),
            'ALLOWED_SORTS' => $this->getAllowedSorts($fields),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);

        $filePath = app_path("Http/Controllers/{$resourceName}Controller.php");
        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, $content);

        return $filePath;
    }

    private function getEagerLoad(array $options): string
    {
        if (empty($options['relationships'])) {
            return '';
        }

        $relations = $this->extractRelationNames($options['relationships']);

        return "\n            ->with(['" . implode("', '", $relations) . "'])";
    }

    private function getEagerLoadShow(string $modelVariable, array $options): string
    {
        if (empty($options['relationships'])) {
            return '';
        }

        $relations = $this->extractRelationNames($options['relationships']);

        return "\${$modelVariable}->load(['" . implode("', '", $relations) . "']);";
    }

    private function getEagerLoadEdit(string $modelVariable, array $options): string
    {
        if (empty($options['relationships'])) {
            return '';
        }

        $relations = $this->extractRelationNames($options['relationships']);

        return "\${$modelVariable}->load(['" . implode("', '", $relations) . "']);";
    }

    private function extractRelationNames(string $relationships): array
    {
        // This will be implemented with relationship parser
        return [];
    }

    private function getAllowedFilters(array $fields): string
    {
        $filterableFields = array_map(fn ($field) => "'{$field['name']}'", $fields);

        return implode(', ', $filterableFields);
    }

    private function getAllowedSorts(array $fields): string
    {
        $sortableFields = array_map(fn ($field) => "'{$field['name']}'", $fields);
        $sortableFields[] = "'id'";
        $sortableFields[] = "'created_at'";

        return implode(', ', $sortableFields);
    }
}
