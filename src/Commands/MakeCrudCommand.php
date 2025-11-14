<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Commands;

use Illuminate\Console\Command;
use Isaac\CrudGenerator\Generators\ControllerGenerator;
use Isaac\CrudGenerator\Generators\MigrationGenerator;
use Isaac\CrudGenerator\Generators\ModelGenerator;
use Isaac\CrudGenerator\Generators\RequestGenerator;
use Isaac\CrudGenerator\Generators\ResourceGenerator;
use Isaac\CrudGenerator\Generators\VueGenerator;
use Isaac\CrudGenerator\Parsers\FieldDefinitionParser;
use Isaac\CrudGenerator\Support\FileRollback;

class MakeCrudCommand extends Command
{
    protected $signature = 'make:crud
                            {resource : The name of the resource (e.g., Product, UserProfile)}
                            {fields* : Field definitions (e.g., name:string price:decimal:nullable)}
                            {--no-soft-deletes : Disable soft deletes in the model}
                            {--no-auditing : Disable auditing in the model}
                            {--no-views : Do not generate Vue components}
                            {--table= : Custom table name}
                            {--force : Overwrite existing files}';

    protected $description = 'Generate a complete CRUD module with Model, Controller, Requests, Resource, Vue components, and Migration';

    private FileRollback $rollback;

    private FieldDefinitionParser $parser;

    public function __construct()
    {
        parent::__construct();
        $this->rollback = new FileRollback();
        $this->parser = new FieldDefinitionParser();
    }

    public function handle(): int
    {
        try {
            // Validate inputs
            $resourceName = $this->argument('resource');
            $fieldDefinitions = $this->argument('fields');

            $this->validateResourceName($resourceName);
            $this->validateFields($fieldDefinitions);

            // Parse fields
            $fields = $this->parseFields($fieldDefinitions);

            // Prepare options
            $options = $this->prepareOptions();

            // Check for existing files if not forcing
            if (! $this->option('force')) {
                $this->checkExistingFiles($resourceName);
            }

            // Start generation
            $this->outputMessage('Generating CRUD files...', 'info');

            // Generate all files atomically
            $this->generateModel($resourceName, $fields, $options);
            $this->generateController($resourceName, $fields, $options);
            $this->generateRequests($resourceName, $fields);
            $this->generateResource($resourceName, $fields, $options);
            $this->generateMigration($resourceName, $fields, $options);

            if (! $this->option('no-views')) {
                $this->generateVueComponents($resourceName, $fields);
            }

            // Commit all files
            $this->rollback->commit();

            // Success message
            $this->outputSuccess($resourceName, $options);

            return self::SUCCESS;
        } catch (\Exception $e) {
            // Rollback on any error
            $this->rollback->rollback();

            $this->outputMessage('Error: ' . $e->getMessage(), 'error');
            $this->outputMessage('All changes have been rolled back.', 'comment');

            return self::FAILURE;
        }
    }

    private function validateResourceName(string $resourceName): void
    {
        // Check empty
        if (empty($resourceName)) {
            throw new \InvalidArgumentException('Resource name cannot be empty.');
        }

        // Check length (1-50 characters)
        if (strlen($resourceName) > 50) {
            throw new \InvalidArgumentException('Resource name must be 50 characters or less.');
        }

        // Check format: alphanumeric and underscores, starting with letter
        if (! preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $resourceName)) {
            throw new \InvalidArgumentException('Resource name must be alphanumeric, start with a letter, and may contain underscores.');
        }

        // Check reserved words
        $reservedWords = config('crud-generator.reserved_words', []);
        if (in_array(strtolower($resourceName), $reservedWords)) {
            throw new \InvalidArgumentException("'{$resourceName}' is a reserved word and cannot be used as a resource name.");
        }
    }

    private function validateFields(array $fieldDefinitions): void
    {
        if (empty($fieldDefinitions)) {
            throw new \InvalidArgumentException('At least one field definition is required.');
        }

        foreach ($fieldDefinitions as $definition) {
            try {
                $this->parser->parse($definition);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException("Invalid field definition '{$definition}': {$e->getMessage()}");
            }
        }
    }

    private function parseFields(array $fieldDefinitions): array
    {
        $fields = [];

        foreach ($fieldDefinitions as $definition) {
            $fields[] = $this->parser->parse($definition);
        }

        return $fields;
    }

    private function prepareOptions(): array
    {
        return [
            'soft_deletes' => ! $this->option('no-soft-deletes'),
            'auditing' => ! $this->option('no-auditing'),
            'generate_views' => ! $this->option('no-views'),
            'table' => $this->option('table'),
            'force' => $this->option('force'),
            'relationships' => '', // Will be populated in Phase 5
        ];
    }

    private function checkExistingFiles(string $resourceName): void
    {
        $modelPath = app_path("Models/{$resourceName}.php");

        if (file_exists($modelPath)) {
            throw new \RuntimeException("Model '{$resourceName}' already exists. Use --force to overwrite.");
        }
    }

    private function generateModel(string $resourceName, array $fields, array $options): void
    {
        $this->outputVerbose('Generating Model...');

        $generator = new ModelGenerator();
        $filePath = $generator->generate($resourceName, $fields, $options);

        $this->rollback->track($filePath);
        $this->outputVerbose("✓ Model created: {$filePath}");
    }

    private function generateController(string $resourceName, array $fields, array $options): void
    {
        $this->outputVerbose('Generating Controller...');

        $generator = new ControllerGenerator();
        $filePath = $generator->generate($resourceName, $fields, $options);

        $this->rollback->track($filePath);
        $this->outputVerbose("✓ Controller created: {$filePath}");
    }

    private function generateRequests(string $resourceName, array $fields): void
    {
        $this->outputVerbose('Generating Form Requests...');

        $generator = new RequestGenerator();
        $filePaths = $generator->generate($resourceName, $fields);

        $this->rollback->track($filePaths['store']);
        $this->rollback->track($filePaths['update']);

        $this->outputVerbose("✓ Store Request created: {$filePaths['store']}");
        $this->outputVerbose("✓ Update Request created: {$filePaths['update']}");
    }

    private function generateResource(string $resourceName, array $fields, array $options): void
    {
        $this->outputVerbose('Generating API Resource...');

        $generator = new ResourceGenerator();
        $filePath = $generator->generate($resourceName, $fields, $options);

        $this->rollback->track($filePath);
        $this->outputVerbose("✓ Resource created: {$filePath}");
    }

    private function generateVueComponents(string $resourceName, array $fields): void
    {
        $this->outputVerbose('Generating Vue components...');

        $generator = new VueGenerator();
        $filePaths = $generator->generate($resourceName, $fields);

        foreach ($filePaths as $key => $path) {
            $this->rollback->track($path);
            $this->outputVerbose("✓ Vue {$key} created: {$path}");
        }
    }

    private function generateMigration(string $resourceName, array $fields, array $options): void
    {
        $this->outputVerbose('Generating Migration...');

        $generator = new MigrationGenerator();
        $filePath = $generator->generate($resourceName, $fields, $options);

        $this->rollback->track($filePath);
        $this->outputVerbose("✓ Migration created: {$filePath}");
    }

    private function outputMessage(string $message, string $type = 'line'): void
    {
        if (! $this->option('quiet') || $type === 'error') {
            $this->{$type}($message);
        }
    }

    private function outputVerbose(string $message): void
    {
        if ($this->option('verbose')) {
            $this->line($message);
        }
    }

    private function outputSuccess(string $resourceName, array $options): void
    {
        if ($this->option('quiet')) {
            return;
        }

        $this->newLine();
        $this->info("✓ CRUD generated successfully for '{$resourceName}'!");
        $this->newLine();

        $fileCount = $options['generate_views'] ? 10 : 6;
        $this->line("Generated {$fileCount} files:");
        $this->line("  • Model");
        $this->line("  • Controller");
        $this->line("  • 2 Form Requests (Store, Update)");
        $this->line("  • API Resource");
        $this->line("  • Migration");

        if ($options['generate_views']) {
            $this->line("  • 4 Vue Components (Index, Create, Edit, Form)");
        }

        $this->newLine();
        $this->comment('Next steps:');
        $this->line('  1. Run: php artisan migrate');
        $this->line("  2. Add routes to routes/web.php");
        $this->line("  3. Register components in your app");
    }
}
