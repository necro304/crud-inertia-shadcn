<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class PublishGuidelinesCommand extends Command
{
    protected $signature = 'crud-generator:publish-guidelines
                            {--force : Overwrite existing guidelines file}';

    protected $description = 'Publish CRUD Inertia Shadcn guidelines to .ai/guidelines directory';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $targetPath = base_path('.ai/guidelines/crud-inertia-shadcn.md');
        $targetDir = dirname($targetPath);
        $stubPath = __DIR__ . '/../../resources/stubs/guidelines/crud-inertia-shadcn.md.stub';

        // Check if file exists and force is not set
        if ($this->files->exists($targetPath) && ! $this->option('force')) {
            $this->warn('Guidelines file already exists!');
            $this->line("Use --force to overwrite: <comment>{$targetPath}</comment>");

            return self::FAILURE;
        }

        // Create .ai/guidelines directory if it doesn't exist
        if (! $this->files->isDirectory($targetDir)) {
            $this->files->makeDirectory($targetDir, 0755, true);
            $this->info("Created directory: {$targetDir}");
        }

        // Copy the stub file to the target location
        $this->files->copy($stubPath, $targetPath);

        $this->newLine();
        $this->info('✓ Guidelines published successfully!');
        $this->newLine();
        $this->line("Published to: <comment>{$targetPath}</comment>");
        $this->newLine();
        $this->comment('This file provides guidance for AI assistants when working with this package.');
        $this->comment('You can customize it to match your specific project needs.');

        return self::SUCCESS;
    }
}
