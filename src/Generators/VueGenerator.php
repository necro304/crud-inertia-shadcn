<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Generators;

use Isaac\CrudGenerator\Support\NamingConverter;
use Isaac\CrudGenerator\Support\StubRenderer;
use Illuminate\Support\Facades\File;

class VueGenerator
{
    public function __construct(
        private readonly StubRenderer $renderer = new StubRenderer()
    ) {
    }

    /**
     * Generate Vue component files
     *
     * @param  string  $resourceName  PascalCase resource name
     * @param  array<array{name: string, type: string, modifiers: array<string>}>  $fields
     * @return array{index: string, create: string, edit: string, form: string}
     */
    public function generate(string $resourceName, array $fields): array
    {
        $vuePath = config('crud-generator.vue_path', 'resources/js/Pages');
        $directoryName = NamingConverter::toPascalCase(NamingConverter::toPlural($resourceName));
        $baseDir = resource_path("js/Pages/{$directoryName}");

        File::ensureDirectoryExists($baseDir);

        $routeName = NamingConverter::toRouteName($resourceName);
        $modelVariable = lcfirst($resourceName);
        $modelVariablePlural = NamingConverter::toPlural($modelVariable);
        $modelPlural = NamingConverter::toPascalCase(NamingConverter::toPlural($resourceName));

        return [
            'index' => $this->generateIndexVue($baseDir, $resourceName, $fields, $routeName, $modelVariable, $modelVariablePlural, $modelPlural),
            'create' => $this->generateCreateVue($baseDir, $resourceName, $fields, $routeName, $modelVariable),
            'edit' => $this->generateEditVue($baseDir, $resourceName, $fields, $routeName, $modelVariable),
            'form' => $this->generateFormVue($baseDir, $resourceName, $fields, $routeName),
        ];
    }

    private function generateIndexVue(string $baseDir, string $resourceName, array $fields, string $routeName, string $modelVariable, string $modelVariablePlural, string $modelPlural): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/index.vue.stub';

        $tokens = [
            'MODEL' => $resourceName,
            'MODEL_PLURAL' => $modelPlural,
            'MODEL_VARIABLE' => $modelVariable,
            'MODEL_VARIABLE_PLURAL' => $modelVariablePlural,
            'ROUTE_NAME' => $routeName,
            'VUE_DIRECTORY' => $modelPlural,
            'TYPE_INTERFACE_FIELDS' => $this->buildTypeInterfaceFields($fields),
            'TABLE_HEADERS' => $this->buildTableHeaders($fields),
            'TABLE_CELLS' => $this->buildTableCells($fields),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);
        $filePath = "{$baseDir}/Index.vue";
        File::put($filePath, $content);

        return $filePath;
    }

    private function generateCreateVue(string $baseDir, string $resourceName, array $fields, string $routeName, string $modelVariable): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/create.vue.stub';

        $tokens = [
            'MODEL' => $resourceName,
            'MODEL_VARIABLE' => $modelVariable,
            'ROUTE_NAME' => $routeName,
            'FORM_INTERFACE_FIELDS' => $this->buildFormInterfaceFields($fields),
            'FORM_INITIAL_VALUES' => $this->buildFormInitialValues($fields),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);
        $filePath = "{$baseDir}/Create.vue";
        File::put($filePath, $content);

        return $filePath;
    }

    private function generateEditVue(string $baseDir, string $resourceName, array $fields, string $routeName, string $modelVariable): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/edit.vue.stub';

        $tokens = [
            'MODEL' => $resourceName,
            'MODEL_VARIABLE' => $modelVariable,
            'ROUTE_NAME' => $routeName,
            'TYPE_INTERFACE_FIELDS' => $this->buildTypeInterfaceFields($fields),
            'FORM_INTERFACE_FIELDS' => $this->buildFormInterfaceFields($fields),
            'FORM_EDIT_VALUES' => $this->buildFormEditValues($fields, $modelVariable),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);
        $filePath = "{$baseDir}/Edit.vue";
        File::put($filePath, $content);

        return $filePath;
    }

    private function generateFormVue(string $baseDir, string $resourceName, array $fields, string $routeName): string
    {
        $stubPath = __DIR__ . '/../../resources/stubs/form.vue.stub';

        $tokens = [
            'MODEL' => $resourceName,
            'ROUTE_NAME' => $routeName,
            'FORM_INTERFACE_FIELDS' => $this->buildFormInterfaceFields($fields),
            'FORM_FIELDS' => $this->buildFormFields($fields),
        ];

        $content = $this->renderer->renderFromFile($stubPath, $tokens);
        $filePath = "{$baseDir}/Form.vue";
        File::put($filePath, $content);

        return $filePath;
    }

    private function buildTypeInterfaceFields(array $fields): string
    {
        $interfaceFields = [];

        foreach ($fields as $field) {
            $tsType = $this->getTypeScriptType($field['type']);
            $nullable = in_array('nullable', $field['modifiers']) ? '?' : '';
            $interfaceFields[] = "{$field['name']}{$nullable}: {$tsType}";
        }

        return implode("\n  ", $interfaceFields);
    }

    private function buildFormInterfaceFields(array $fields): string
    {
        return $this->buildTypeInterfaceFields($fields);
    }

    private function buildFormInitialValues(array $fields): string
    {
        $values = [];

        foreach ($fields as $field) {
            $defaultValue = $this->getDefaultValue($field['type']);
            $values[] = "{$field['name']}: {$defaultValue}";
        }

        return implode(",\n  ", $values);
    }

    private function buildFormEditValues(array $fields, string $modelVariable): string
    {
        $values = [];

        foreach ($fields as $field) {
            $values[] = "{$field['name']}: props.{$modelVariable}.{$field['name']}";
        }

        return implode(",\n  ", $values);
    }

    private function buildTableHeaders(array $fields): string
    {
        $headers = [];

        foreach ($fields as $field) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $headers[] = "<TableHead>{$label}</TableHead>";
        }

        return implode("\n            ", $headers);
    }

    private function buildTableCells(array $fields): string
    {
        $cells = [];

        foreach ($fields as $field) {
            if ($field['type'] === 'boolean') {
                $cells[] = "<TableCell>{{ item.{$field['name']} ? 'Yes' : 'No' }}</TableCell>";
            } else {
                $cells[] = "<TableCell>{{ item.{$field['name']} }}</TableCell>";
            }
        }

        return implode("\n            ", $cells);
    }

    private function buildFormFields(array $fields): string
    {
        $formFields = [];

        foreach ($fields as $field) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $component = $this->getFormComponent($field['type']);
            $required = ! in_array('nullable', $field['modifiers']);

            $formFields[] = $this->generateFieldTemplate($field['name'], $label, $component, $required);
        }

        return implode("\n\n        ", $formFields);
    }

    private function generateFieldTemplate(string $name, string $label, string $component, bool $required): string
    {
        if ($component === 'Textarea') {
            return <<<VUE
<div>
          <Label for="{$name}">{$label}</Label>
          <Textarea
            id="{$name}"
            v-model="form.data.{$name}"
            :class="cn(form.errors.{$name} && 'border-destructive')"
          />
          <p v-if="form.errors.{$name}" class="text-sm text-destructive">
            {{ form.errors.{$name} }}
          </p>
        </div>
VUE;
        }

        if ($component === 'Checkbox') {
            return <<<VUE
<div class="flex items-center space-x-2">
          <Checkbox
            id="{$name}"
            :checked="form.data.{$name}"
            @update:checked="form.data.{$name} = \$event"
          />
          <Label for="{$name}">{$label}</Label>
          <p v-if="form.errors.{$name}" class="text-sm text-destructive">
            {{ form.errors.{$name} }}
          </p>
        </div>
VUE;
        }

        return <<<VUE
<div>
          <Label for="{$name}">{$label}</Label>
          <Input
            id="{$name}"
            v-model="form.data.{$name}"
            type="{$this->getInputType($component)}"
            :class="cn(form.errors.{$name} && 'border-destructive')"
          />
          <p v-if="form.errors.{$name}" class="text-sm text-destructive">
            {{ form.errors.{$name} }}
          </p>
        </div>
VUE;
    }

    private function getTypeScriptType(string $phpType): string
    {
        return match ($phpType) {
            'string', 'text' => 'string',
            'integer' => 'number',
            'decimal' => 'number',
            'boolean' => 'boolean',
            'date', 'datetime', 'timestamp' => 'string',
            'json' => 'Record<string, any>',
            default => 'string',
        };
    }

    private function getDefaultValue(string $phpType): string
    {
        return match ($phpType) {
            'string', 'text' => "''",
            'integer', 'decimal' => '0',
            'boolean' => 'false',
            'date', 'datetime', 'timestamp' => "''",
            'json' => '{}',
            default => "''",
        };
    }

    private function getFormComponent(string $phpType): string
    {
        return match ($phpType) {
            'text' => 'Textarea',
            'boolean' => 'Checkbox',
            default => 'Input',
        };
    }

    private function getInputType(string $component): string
    {
        return match ($component) {
            'Textarea' => 'text',
            'Checkbox' => 'checkbox',
            default => 'text',
        };
    }
}
