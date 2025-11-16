<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator;

use Isaac\CrudGenerator\Generators\ControllerGenerator;
use Isaac\CrudGenerator\Generators\MigrationGenerator;
use Isaac\CrudGenerator\Generators\ModelGenerator;
use Isaac\CrudGenerator\Generators\RequestGenerator;
use Isaac\CrudGenerator\Generators\ResourceGenerator;
use Isaac\CrudGenerator\Generators\VueGenerator;
use Isaac\CrudGenerator\Parsers\FieldDefinitionParser;
use Isaac\CrudGenerator\Support\CrudGenerationResult;
use Isaac\CrudGenerator\Support\FileRollback;

final class CrudGenerator
{
    public function __construct(
        private FieldDefinitionParser $parser,
        private ModelGenerator $modelGenerator,
        private ControllerGenerator $controllerGenerator,
        private RequestGenerator $requestGenerator,
        private ResourceGenerator $resourceGenerator,
        private MigrationGenerator $migrationGenerator,
        private VueGenerator $vueGenerator,
    ) {}

    /**
     * @param array<int, string> $fieldDefinitions
     * @param array<string, mixed> $options
     */
    public function generate(string $resourceName, array $fieldDefinitions, array $options = []): CrudGenerationResult
    {
        $options = $this->prepareOptions($options);

        $this->validateResourceName($resourceName);
        $fields = $this->parseFields($fieldDefinitions);

        if (! $options['force']) {
            $this->checkExistingFiles($resourceName);
        }

        $rollback = new FileRollback();
        $files = [];

        try {
            $files['model'] = $this->generateModel($resourceName, $fields, $options, $rollback);
            $files['controller'] = $this->generateController($resourceName, $fields, $options, $rollback);
            $files['requests'] = $this->generateRequests($resourceName, $fields, $rollback);
            $files['resource'] = $this->generateResource($resourceName, $fields, $options, $rollback);
            $files['migration'] = $this->generateMigration($resourceName, $fields, $options, $rollback);

            if ($options['generate_views']) {
                $files['views'] = $this->generateVueComponents($resourceName, $fields, $rollback);
            }

            $rollback->commit();

            return new CrudGenerationResult($resourceName, $files, $options);
        } catch (\Throwable $e) {
            $rollback->rollback();
            throw $e;
        }
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @param array<string, mixed> $options
     */
    private function generateModel(string $resourceName, array $fields, array $options, FileRollback $rollback): string
    {
        $filePath = $this->modelGenerator->generate($resourceName, $fields, $options);
        $rollback->track($filePath);

        return $filePath;
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @param array<string, mixed> $options
     */
    private function generateController(string $resourceName, array $fields, array $options, FileRollback $rollback): string
    {
        $filePath = $this->controllerGenerator->generate($resourceName, $fields, $options);
        $rollback->track($filePath);

        return $filePath;
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @return array<string, string>
     */
    private function generateRequests(string $resourceName, array $fields, FileRollback $rollback): array
    {
        $filePaths = $this->requestGenerator->generate($resourceName, $fields);

        foreach ($filePaths as $path) {
            $rollback->track($path);
        }

        return $filePaths;
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @param array<string, mixed> $options
     */
    private function generateResource(string $resourceName, array $fields, array $options, FileRollback $rollback): string
    {
        $filePath = $this->resourceGenerator->generate($resourceName, $fields, $options);
        $rollback->track($filePath);

        return $filePath;
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @param array<string, mixed> $options
     */
    private function generateMigration(string $resourceName, array $fields, array $options, FileRollback $rollback): string
    {
        $filePath = $this->migrationGenerator->generate($resourceName, $fields, $options);
        $rollback->track($filePath);

        return $filePath;
    }

    /**
     * @param array<int, array<string, mixed>> $fields
     * @return array<string, string>
     */
    private function generateVueComponents(string $resourceName, array $fields, FileRollback $rollback): array
    {
        $filePaths = $this->vueGenerator->generate($resourceName, $fields);

        foreach ($filePaths as $path) {
            $rollback->track($path);
        }

        return $filePaths;
    }

    /**
     * @param array<int, string> $fieldDefinitions
     * @return array<int, array<string, mixed>>
     */
    private function parseFields(array $fieldDefinitions): array
    {
        if (empty($fieldDefinitions)) {
            throw new \InvalidArgumentException('At least one field definition is required.');
        }

        $fields = [];

        foreach ($fieldDefinitions as $definition) {
            try {
                $fields[] = $this->parser->parse($definition);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("Invalid field definition '{$definition}': {$e->getMessage()}");
            }
        }

        return $fields;
    }

    private function validateResourceName(string $resourceName): void
    {
        if ($resourceName === '') {
            throw new \InvalidArgumentException('Resource name cannot be empty.');
        }

        if (strlen($resourceName) > 50) {
            throw new \InvalidArgumentException('Resource name must be 50 characters or less.');
        }

        if (! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $resourceName)) {
            throw new \InvalidArgumentException('Resource name must be alphanumeric, start with a letter, and may contain underscores.');
        }

        $reservedWords = array_map('strtolower', config('crud-generator.reserved_words', []));

        if (in_array(strtolower($resourceName), $reservedWords, true)) {
            throw new \InvalidArgumentException("'{$resourceName}' is a reserved word and cannot be used as a resource name.");
        }
    }

    private function checkExistingFiles(string $resourceName): void
    {
        $filesToCheck = [
            app_path("Models/{$resourceName}.php") => 'Model',
            app_path("Http/Controllers/{$resourceName}Controller.php") => 'Controller',
            app_path("Http/Requests/Store{$resourceName}Request.php") => 'StoreRequest',
            app_path("Http/Requests/Update{$resourceName}Request.php") => 'UpdateRequest',
            app_path("Http/Resources/{$resourceName}Resource.php") => 'Resource',
        ];

        foreach ($filesToCheck as $path => $type) {
            if (file_exists($path)) {
                throw new \RuntimeException("{$type} already exists at: {$path}. Use force option to overwrite.");
            }
        }
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    private function prepareOptions(array $options): array
    {
        $defaults = [
            'soft_deletes' => true,
            'auditing' => true,
            'generate_views' => true,
            'table' => null,
            'force' => false,
            'relationships' => '',
        ];

        return array_merge($defaults, $options);
    }
}
