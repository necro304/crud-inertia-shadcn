<?php

use Illuminate\Support\Facades\File;
use Isaac\CrudGenerator\Commands\MakeCrudCommand;

afterEach(function () {
    cleanupModifierTestFiles();
});

test('nullable modifier makes field nullable in migration', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['email:string:nullable'],
    ])->assertSuccessful();

    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    $migrationContent = File::get($migrationFiles[0]);

    expect($migrationContent)->toContain('$table->string(\'email\')->nullable()');
});

test('nullable modifier adds nullable to validation rules', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['email:string:nullable'],
    ])->assertSuccessful();

    $requestContent = File::get(app_path('Http/Requests/StoreProductRequest.php'));

    expect($requestContent)->toContain('\'email\' => [\'nullable\', \'string\', \'max:255\']');
    expect($requestContent)->not->toContain('\'email\' => [\'required\'');
});

test('unique modifier adds unique constraint in migration', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['slug:string:unique'],
    ])->assertSuccessful();

    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    $migrationContent = File::get($migrationFiles[0]);

    expect($migrationContent)->toContain('$table->string(\'slug\')->unique()');
});

test('unique modifier adds unique validation rule', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['slug:string:unique'],
    ])->assertSuccessful();

    $requestContent = File::get(app_path('Http/Requests/StoreProductRequest.php'));

    expect($requestContent)->toContain('\'slug\' => [\'required\', \'string\', \'max:255\', \'unique:products,slug\']');
});

test('combined nullable and unique modifiers work together in migration', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['email:string:nullable:unique'],
    ])->assertSuccessful();

    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    $migrationContent = File::get($migrationFiles[0]);

    expect($migrationContent)->toContain('$table->string(\'email\')->nullable()->unique()');
});

test('combined nullable and unique modifiers work together in validation', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['email:string:nullable:unique'],
    ])->assertSuccessful();

    $requestContent = File::get(app_path('Http/Requests/StoreProductRequest.php'));

    expect($requestContent)->toContain('\'email\' => [\'nullable\', \'string\', \'max:255\', \'unique:products,email\']');
});

test('update request has unique validation with ignore clause', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['email:string:unique'],
    ])->assertSuccessful();

    $updateRequestContent = File::get(app_path('Http/Requests/UpdateProductRequest.php'));

    // Should contain Rule::unique()->ignore() pattern
    expect($updateRequestContent)->toContain("Rule::unique('products', 'email')");
    expect($updateRequestContent)->toContain('ignore');
});

test('multiple fields with different modifiers', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => [
            'name:string',
            'email:string:nullable:unique',
            'slug:string:unique',
            'description:text:nullable',
        ],
    ])->assertSuccessful();

    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    $migrationContent = File::get($migrationFiles[0]);

    expect($migrationContent)->toContain('$table->string(\'name\')');
    expect($migrationContent)->toContain('$table->string(\'email\')->nullable()->unique()');
    expect($migrationContent)->toContain('$table->string(\'slug\')->unique()');
    expect($migrationContent)->toContain('$table->text(\'description\')->nullable()');
});

// Helper function
function cleanupModifierTestFiles(): void
{
    File::delete(app_path('Models/Product.php'));
    File::delete(app_path('Http/Controllers/ProductController.php'));
    File::delete(app_path('Http/Requests/StoreProductRequest.php'));
    File::delete(app_path('Http/Requests/UpdateProductRequest.php'));
    File::delete(app_path('Http/Resources/ProductResource.php'));
    File::deleteDirectory(resource_path('js/Pages/Products'));

    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    foreach ($migrationFiles as $file) {
        File::delete($file);
    }
}
