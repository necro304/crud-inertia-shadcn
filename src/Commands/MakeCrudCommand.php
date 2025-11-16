<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn\Commands;

use Illuminate\Console\Command;
use Necro304\CrudInertiaShadcn\CrudGenerator;
use Necro304\CrudInertiaShadcn\Support\CrudGenerationResult;

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

    public function __construct(private readonly CrudGenerator $generator)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        try {
            $resourceName = $this->argument('resource');
            $fieldDefinitions = $this->argument('fields');
            $result = $this->generator->generate($resourceName, $fieldDefinitions, $this->commandOptions());

            $this->outputSuccess($result);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->comment('All changes have been rolled back.');

            return self::FAILURE;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function commandOptions(): array
    {
        return [
            'soft_deletes' => ! $this->option('no-soft-deletes'),
            'auditing' => ! $this->option('no-auditing'),
            'generate_views' => ! $this->option('no-views'),
            'table' => $this->option('table'),
            'force' => (bool) $this->option('force'),
            'relationships' => '',
        ];
    }

    private function outputSuccess(CrudGenerationResult $result): void
    {
        if ($this->option('quiet')) {
            return;
        }

        $this->newLine();
        $this->info("✓ CRUD generated successfully for '{$result->resourceName}'!");
        $this->newLine();

        $this->line(sprintf('Generated %d files:', $result->fileCount()));

        foreach ($result->flatFiles() as $type => $path) {
            $this->line(sprintf('  • %s -> %s', $type, $path));
        }

        $this->newLine();
        $this->comment('Next steps:');
        $this->line('  1. Run: php artisan migrate');
        $this->line('  2. Add routes to routes/web.php');
        $this->line('  3. Register components in your app');
    }
}
