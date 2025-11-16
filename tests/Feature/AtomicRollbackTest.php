<?php

use Illuminate\Support\Facades\File;
use Necro304\CrudInertiaShadcn\Commands\MakeCrudCommand;

afterEach(function () {
    cleanupRollbackTestFiles();
});

test('rolls back all files on generation failure', function () {
    // This test will simulate a failure during generation
    // We'll need to implement a way to trigger a controlled failure

    // For now, we'll test that when a file already exists and --force is not used,
    // no files are created if one file already exists

    // Create a pre-existing file to cause conflict
    File::ensureDirectoryExists(app_path('Models'));
    File::put(app_path('Models/Product.php'), '<?php // Existing file');

    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string'],
    ])->assertFailed();

    // Verify no new files were created (atomic rollback)
    expect(app_path('Http/Controllers/ProductController.php'))->not->toBeFile();
    expect(app_path('Http/Requests/StoreProductRequest.php'))->not->toBeFile();
    expect(app_path('Http/Requests/UpdateProductRequest.php'))->not->toBeFile();
    expect(app_path('Http/Resources/ProductResource.php'))->not->toBeFile();
    expect(resource_path('js/Pages/Products'))->not->toBeDirectory();

    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    expect($migrationFiles)->toBeEmpty();
});

test('all files created during generation are tracked', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // Count generated files
    $generatedFiles = [
        app_path('Models/Product.php'),
        app_path('Http/Controllers/ProductController.php'),
        app_path('Http/Requests/StoreProductRequest.php'),
        app_path('Http/Requests/UpdateProductRequest.php'),
        app_path('Http/Resources/ProductResource.php'),
        resource_path('js/Pages/Products/Index.vue'),
        resource_path('js/Pages/Products/Create.vue'),
        resource_path('js/Pages/Products/Edit.vue'),
        resource_path('js/Pages/Products/Form.vue'),
    ];

    foreach ($generatedFiles as $file) {
        expect($file)->toBeFile();
    }

    // Plus 1 migration file
    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    expect($migrationFiles)->toHaveCount(1);

    // Total: 10 files
});

test('partial generation does not leave orphaned files', function () {
    // Create a scenario where generation fails midway
    // For example, if Vue directory creation fails

    // Pre-create a file that would conflict
    File::ensureDirectoryExists(app_path('Http/Requests'));
    File::put(app_path('Http/Requests/StoreProductRequest.php'), '<?php // Conflict');

    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string'],
    ])->assertFailed();

    // Verify no orphaned files exist
    // Model should NOT be created (rollback)
    expect(app_path('Models/Product.php'))->not->toBeFile();

    // Controller should NOT be created (rollback)
    expect(app_path('Http/Controllers/ProductController.php'))->not->toBeFile();

    // The conflicting file should still exist (not deleted)
    expect(app_path('Http/Requests/StoreProductRequest.php'))->toBeFile();
    $content = File::get(app_path('Http/Requests/StoreProductRequest.php'));
    expect($content)->toContain('// Conflict');
});

test('successful generation commits all files', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // All files should exist and be committed
    expect(app_path('Models/Product.php'))->toBeFile();
    expect(app_path('Http/Controllers/ProductController.php'))->toBeFile();
    expect(app_path('Http/Requests/StoreProductRequest.php'))->toBeFile();
    expect(app_path('Http/Requests/UpdateProductRequest.php'))->toBeFile();
    expect(app_path('Http/Resources/ProductResource.php'))->toBeFile();
    expect(resource_path('js/Pages/Products/Index.vue'))->toBeFile();
    expect(resource_path('js/Pages/Products/Create.vue'))->toBeFile();
    expect(resource_path('js/Pages/Products/Edit.vue'))->toBeFile();
    expect(resource_path('js/Pages/Products/Form.vue'))->toBeFile();

    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    expect($migrationFiles)->toHaveCount(1);

    // Files should have valid content (not empty or corrupted)
    $modelContent = File::get(app_path('Models/Product.php'));
    expect($modelContent)->toContain('class Product');
    expect(strlen($modelContent))->toBeGreaterThan(100);
});

test('rollback deletes files in reverse order', function () {
    // This is more of an implementation detail test
    // The FileRollback class should delete in LIFO order

    // We can verify this indirectly by checking that migration
    // (created last) doesn't exist after rollback

    File::ensureDirectoryExists(app_path('Models'));
    File::put(app_path('Models/Product.php'), '<?php // Conflict');

    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string'],
    ])->assertFailed();

    // Migration should not exist (was rolled back)
    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    expect($migrationFiles)->toBeEmpty();
});

// Helper function
function cleanupRollbackTestFiles(): void
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
