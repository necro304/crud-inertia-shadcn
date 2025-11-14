<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Generators;

use Isaac\CrudGenerator\Support\NamingConverter;
use Isaac\CrudGenerator\Support\StubRenderer;
use Illuminate\Support\Facades\File;

class MigrationGenerator
{
    public function __construct(
        private readonly StubRenderer $renderer = new StubRenderer()
    ) {
    }

    /**
     * Generate Migration file
     *
     * @param  string  $resourceName  PascalCase resource name
     * @param  array<array{name: string, type: string, modifiers: array<string>}>  $fields
     * @param  array<string, mixed>  $options
     * @return string  Path to generated file
     */
    public function generate(string $resourceName, array $fields, array $options = []): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/migration.stub';
        $tableName = $options['table'] ?? NamingConverter::toTableName($resourceName);

        $tokens = [
            'TABLE' => $tableName,
            'COLUMNS' => $this->buildColumns($fields),
            'SOFT_DELETES' => $this->getSoftDeletes($options),
            'INDEXES' => $this->buildIndexes($fields),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);

        // Generate migration filename with timestamp
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_create_{$tableName}_table.php";
        $migrationsPath = config('crud-generator.migrations_path', 'database/migrations');
        $filePath = base_path("{$migrationsPath}/{$filename}");

        File::ensureDirectoryExists(dirname($filePath));
        File::put($filePath, $content);

        return $filePath;
    }

    private function buildColumns(array $fields): string
    {
        $columns = [];

        foreach ($fields as $field) {
            $columnDef = $this->buildColumnDefinition($field);
            $columns[] = $columnDef;
        }

        return implode("\n            ", $columns);
    }

    private function buildColumnDefinition(array $field): string
    {
        $type = $this->getColumnType($field['type']);
        $column = "\$table->{$type}('{$field['name']}')";

        // Add modifiers
        if (in_array('nullable', $field['modifiers'])) {
            $column .= '->nullable()';
        }

        if (in_array('unique', $field['modifiers'])) {
            $column .= '->unique()';
        }

        $column .= ';';

        return $column;
    }

    private function getColumnType(string $phpType): string
    {
        $fieldTypes = config('crud-generator.field_types', []);

        if (isset($fieldTypes[$phpType]['column'])) {
            return $fieldTypes[$phpType]['column'];
        }

        return match ($phpType) {
            'string' => 'string',
            'text' => 'text',
            'integer' => 'integer',
            'decimal' => 'decimal',
            'boolean' => 'boolean',
            'date' => 'date',
            'datetime' => 'dateTime',
            'timestamp' => 'timestamp',
            'json' => 'json',
            default => 'string',
        };
    }

    private function getSoftDeletes(array $options): string
    {
        $useSoftDeletes = $options['soft_deletes'] ?? config('crud-generator.defaults.soft_deletes', true);

        return $useSoftDeletes ? '$table->softDeletes();' : '';
    }

    private function buildIndexes(array $fields): string
    {
        $indexes = [];

        foreach ($fields as $field) {
            // Unique modifier already adds unique constraint in column definition
            // Additional composite indexes would be added here if needed
        }

        return implode("\n            ", $indexes);
    }
}
