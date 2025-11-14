<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Generators;

use Isaac\CrudGenerator\Support\StubRenderer;
use Illuminate\Support\Facades\File;

class ResourceGenerator
{
    public function __construct(
        private readonly StubRenderer $renderer = new StubRenderer()
    ) {
    }

    /**
     * Generate API Resource file
     *
     * @param  string  $resourceName  PascalCase resource name
     * @param  array<array{name: string, type: string, modifiers: array<string>}>  $fields
     * @param  array<string, mixed>  $options
     * @return string  Path to generated file
     */
    public function generate(string $resourceName, array $fields, array $options = []): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/resource.stub';
        $namespace = config('crud-generator.resource_namespace', 'App\Http\Resources');

        $tokens = [
            'NAMESPACE' => $namespace,
            'CLASS' => $resourceName . 'Resource',
            'RESOURCE_FIELDS' => $this->buildResourceFields($fields),
            'SOFT_DELETE_FIELD' => $this->getSoftDeleteField($options),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);

        $filePath = app_path("Http/Resources/{$resourceName}Resource.php");
        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, $content);

        return $filePath;
    }

    private function buildResourceFields(array $fields): string
    {
        $resourceFields = [];

        foreach ($fields as $field) {
            $resourceFields[] = "'{$field['name']}' => \$this->{$field['name']}";
        }

        return implode(",\n            ", $resourceFields) . ',';
    }

    private function getSoftDeleteField(array $options): string
    {
        $useSoftDeletes = $options['soft_deletes'] ?? config('crud-generator.defaults.soft_deletes', true);

        if ($useSoftDeletes) {
            return "'deleted_at' => \$this->deleted_at?->toISOString(),";
        }

        return '';
    }
}
