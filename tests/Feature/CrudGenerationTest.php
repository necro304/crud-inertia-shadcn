<?php

use Illuminate\Support\Facades\File;
use Isaac\CrudGenerator\Commands\MakeCrudCommand;

beforeEach(function () {
    // Clean up any generated files before each test
    cleanupGeneratedFiles();
});

afterEach(function () {
    // Clean up after each test
    cleanupGeneratedFiles();
});

test('generates all 10 CRUD files with correct naming', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string', 'price:decimal'],
    ])->assertSuccessful();

    // Verify Model
    expect(app_path('Models/Product.php'))->toBeFile();

    // Verify Controller
    expect(app_path('Http/Controllers/ProductController.php'))->toBeFile();

    // Verify Form Requests
    expect(app_path('Http/Requests/StoreProductRequest.php'))->toBeFile();
    expect(app_path('Http/Requests/UpdateProductRequest.php'))->toBeFile();

    // Verify Resource
    expect(app_path('Http/Resources/ProductResource.php'))->toBeFile();

    // Verify Vue Components
    expect(resource_path('js/Pages/Products/Index.vue'))->toBeFile();
    expect(resource_path('js/Pages/Products/Create.vue'))->toBeFile();
    expect(resource_path('js/Pages/Products/Edit.vue'))->toBeFile();
    expect(resource_path('js/Pages/Products/Form.vue'))->toBeFile();

    // Verify Migration
    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    expect($migrationFiles)->toHaveCount(1);
});

test('generated Model has correct structure', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string', 'price:decimal'],
    ])->assertSuccessful();

    $modelContent = File::get(app_path('Models/Product.php'));

    expect($modelContent)->toContain('namespace App\Models');
    expect($modelContent)->toContain('class Product extends Model');
    expect($modelContent)->toContain('use SoftDeletes');
    expect($modelContent)->toContain('use Auditable');
    expect($modelContent)->toContain('protected $fillable');
    expect($modelContent)->toContain('protected $casts');
});

test('generated Controller has correct structure', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string', 'price:decimal'],
    ])->assertSuccessful();

    $controllerContent = File::get(app_path('Http/Controllers/ProductController.php'));

    expect($controllerContent)->toContain('namespace App\Http\Controllers');
    expect($controllerContent)->toContain('class ProductController extends Controller');
    expect($controllerContent)->toContain('public function index(');
    expect($controllerContent)->toContain('public function create(');
    expect($controllerContent)->toContain('public function store(Store');
    expect($controllerContent)->toContain('public function show(');
    expect($controllerContent)->toContain('public function edit(');
    expect($controllerContent)->toContain('public function update(Update');
    expect($controllerContent)->toContain('public function destroy(');
});

test('generated Migration has correct structure', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string', 'price:decimal'],
    ])->assertSuccessful();

    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    $migrationContent = File::get($migrationFiles[0]);

    expect($migrationContent)->toContain('Schema::create(\'products\'');
    expect($migrationContent)->toContain('$table->id()');
    expect($migrationContent)->toContain('$table->string(\'name\')');
    expect($migrationContent)->toContain('$table->decimal(\'price\')');
    expect($migrationContent)->toContain('$table->timestamps()');
    expect($migrationContent)->toContain('$table->softDeletes()');
});

// Helper function to clean up generated files
function cleanupGeneratedFiles(): void
{
    $paths = [
        app_path('Models/Product.php'),
        app_path('Http/Controllers/ProductController.php'),
        app_path('Http/Requests/StoreProductRequest.php'),
        app_path('Http/Requests/UpdateProductRequest.php'),
        app_path('Http/Resources/ProductResource.php'),
        resource_path('js/Pages/Products'),
        database_path('migrations'),
    ];

    foreach ($paths as $path) {
        if (File::isDirectory($path)) {
            File::deleteDirectory($path);
        } elseif (File::exists($path)) {
            File::delete($path);
        }
    }

    // Clean up migration files
    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    foreach ($migrationFiles as $file) {
        File::delete($file);
    }
}
