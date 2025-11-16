<?php

use Illuminate\Support\Facades\File;
use Necro304\CrudInertiaShadcn\Commands\MakeCrudCommand;

afterEach(function () {
    cleanupTestFiles();
});

test('supports string field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['title:string'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->string(\'title\')');
});

test('supports text field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['description:text'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->text(\'description\')');
});

test('supports integer field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['quantity:integer'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->integer(\'quantity\')');
});

test('supports decimal field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['price:decimal'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->decimal(\'price\')');
});

test('supports boolean field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['is_active:boolean'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->boolean(\'is_active\')');
});

test('supports date field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['birth_date:date'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->date(\'birth_date\')');
});

test('supports datetime field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['published_at:datetime'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->dateTime(\'published_at\')');
});

test('supports timestamp field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['verified_at:timestamp'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->timestamp(\'verified_at\')');
});

test('supports json field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => ['metadata:json'],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->json(\'metadata\')');
});

test('supports multiple fields with different types', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => [
            'name:string',
            'description:text',
            'quantity:integer',
            'price:decimal',
            'is_active:boolean',
        ],
    ])->assertSuccessful();

    $migrationContent = getMigrationContent('test_models');

    expect($migrationContent)->toContain('$table->string(\'name\')');
    expect($migrationContent)->toContain('$table->text(\'description\')');
    expect($migrationContent)->toContain('$table->integer(\'quantity\')');
    expect($migrationContent)->toContain('$table->decimal(\'price\')');
    expect($migrationContent)->toContain('$table->boolean(\'is_active\')');
});

test('generates correct validation rules for each field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'TestModel',
        'fields' => [
            'name:string',
            'quantity:integer',
            'price:decimal',
            'is_active:boolean',
        ],
    ])->assertSuccessful();

    $requestContent = File::get(app_path('Http/Requests/StoreTestModelRequest.php'));

    expect($requestContent)->toContain('\'name\' => [\'required\', \'string\', \'max:255\']');
    expect($requestContent)->toContain('\'quantity\' => [\'required\', \'integer\']');
    expect($requestContent)->toContain('\'price\' => [\'required\', \'numeric\']');
    expect($requestContent)->toContain('\'is_active\' => [\'required\', \'boolean\']');
});

// Helper functions
function getMigrationContent(string $tableName): string
{
    $migrationFiles = File::glob(database_path("migrations/*_create_{$tableName}_table.php"));

    return File::get($migrationFiles[0]);
}

function cleanupTestFiles(): void
{
    File::delete(app_path('Models/TestModel.php'));
    File::delete(app_path('Http/Controllers/TestModelController.php'));
    File::delete(app_path('Http/Requests/StoreTestModelRequest.php'));
    File::delete(app_path('Http/Requests/UpdateTestModelRequest.php'));
    File::delete(app_path('Http/Resources/TestModelResource.php'));
    File::deleteDirectory(resource_path('js/Pages/TestModels'));

    $migrationFiles = File::glob(database_path('migrations/*_create_test_models_table.php'));
    foreach ($migrationFiles as $file) {
        File::delete($file);
    }
}
