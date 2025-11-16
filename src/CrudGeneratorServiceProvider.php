<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn;

use Necro304\CrudInertiaShadcn\Commands\MakeCrudCommand;
use Necro304\CrudInertiaShadcn\Commands\PublishGuidelinesCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CrudGeneratorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-crud-generator')
            ->hasConfigFile('crud-generator')
            ->hasCommands([
                MakeCrudCommand::class,
                PublishGuidelinesCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(CrudGenerator::class);
        $this->app->alias(CrudGenerator::class, 'crud-generator');
    }

    public function packageBooted(): void
    {
        // Publish stubs
        $this->publishes([
            __DIR__ . '/../resources/stubs' => base_path('stubs/crud-generator'),
        ], 'crud-generator-stubs');
    }
}
