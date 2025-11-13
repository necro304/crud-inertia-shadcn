<?php

namespace isaac@example.com\crud-generator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use isaac@example.com\crud-generator\Commands\crud-generatorCommand;

class crud-generatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('isaac')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_isaac_table')
            ->hasCommand(crud-generatorCommand::class);
    }
}
