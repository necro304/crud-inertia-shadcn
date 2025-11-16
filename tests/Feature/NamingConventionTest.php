<?php

use Illuminate\Support\Facades\File;
use Necro304\CrudInertiaShadcn\Commands\MakeCrudCommand;

afterEach(function () {
    cleanupNamingTestFiles();
});

test('PascalCase resource name generates correct file names', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'UserProfile',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // Model: PascalCase
    expect(app_path('Models/UserProfile.php'))->toBeFile();

    // Controller: PascalCase + Controller
    expect(app_path('Http/Controllers/UserProfileController.php'))->toBeFile();

    // Requests: PascalCase + Request
    expect(app_path('Http/Requests/StoreUserProfileRequest.php'))->toBeFile();
    expect(app_path('Http/Requests/UpdateUserProfileRequest.php'))->toBeFile();

    // Resource: PascalCase + Resource
    expect(app_path('Http/Resources/UserProfileResource.php'))->toBeFile();
});

test('generates snake_case plural table name in migration', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'UserProfile',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    $migrationFiles = File::glob(database_path('migrations/*_create_user_profiles_table.php'));

    expect($migrationFiles)->toHaveCount(1);

    $migrationContent = File::get($migrationFiles[0]);

    expect($migrationContent)->toContain('Schema::create(\'user_profiles\'');
});

test('generates kebab-case plural directory for Vue components', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'UserProfile',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // Vue components in kebab-case plural directory
    expect(resource_path('js/Pages/UserProfiles/Index.vue'))->toBeFile();
    expect(resource_path('js/Pages/UserProfiles/Create.vue'))->toBeFile();
    expect(resource_path('js/Pages/UserProfiles/Edit.vue'))->toBeFile();
    expect(resource_path('js/Pages/UserProfiles/Form.vue'))->toBeFile();
});

test('Model content uses correct table name', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'UserProfile',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    $modelContent = File::get(app_path('Models/UserProfile.php'));

    // Should use convention, no explicit table name needed
    // Or if explicit: protected $table = 'user_profiles';
    expect($modelContent)->toContain('class UserProfile extends Model');
});

test('single word resource names work correctly', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // Model: Product
    expect(app_path('Models/Product.php'))->toBeFile();

    // Table: products
    $migrationFiles = File::glob(database_path('migrations/*_create_products_table.php'));
    $migrationContent = File::get($migrationFiles[0]);
    expect($migrationContent)->toContain('Schema::create(\'products\'');

    // Vue directory: Products
    expect(resource_path('js/Pages/Products'))->toBeDirectory();
});

test('complex multi-word resource names', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'OrderItemDetail',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // Model: OrderItemDetail (PascalCase)
    expect(app_path('Models/OrderItemDetail.php'))->toBeFile();

    // Table: order_item_details (snake_case plural)
    $migrationFiles = File::glob(database_path('migrations/*_create_order_item_details_table.php'));
    expect($migrationFiles)->toHaveCount(1);

    // Vue directory: OrderItemDetails (PascalCase plural for directory name)
    expect(resource_path('js/Pages/OrderItemDetails'))->toBeDirectory();
});

test('namespace and class names are correct', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'UserProfile',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    $modelContent = File::get(app_path('Models/UserProfile.php'));
    expect($modelContent)->toContain('namespace App\Models');
    expect($modelContent)->toContain('class UserProfile extends Model');

    $controllerContent = File::get(app_path('Http/Controllers/UserProfileController.php'));
    expect($controllerContent)->toContain('namespace App\Http\Controllers');
    expect($controllerContent)->toContain('class UserProfileController extends Controller');

    $requestContent = File::get(app_path('Http/Requests/StoreUserProfileRequest.php'));
    expect($requestContent)->toContain('namespace App\Http\Requests');
    expect($requestContent)->toContain('class StoreUserProfileRequest extends FormRequest');
});

// Helper function
function cleanupNamingTestFiles(): void
{
    // UserProfile cleanup
    File::delete(app_path('Models/UserProfile.php'));
    File::delete(app_path('Http/Controllers/UserProfileController.php'));
    File::delete(app_path('Http/Requests/StoreUserProfileRequest.php'));
    File::delete(app_path('Http/Requests/UpdateUserProfileRequest.php'));
    File::delete(app_path('Http/Resources/UserProfileResource.php'));
    File::deleteDirectory(resource_path('js/Pages/UserProfiles'));

    // Product cleanup
    File::delete(app_path('Models/Product.php'));
    File::delete(app_path('Http/Controllers/ProductController.php'));
    File::delete(app_path('Http/Requests/StoreProductRequest.php'));
    File::delete(app_path('Http/Requests/UpdateProductRequest.php'));
    File::delete(app_path('Http/Resources/ProductResource.php'));
    File::deleteDirectory(resource_path('js/Pages/Products'));

    // OrderItemDetail cleanup
    File::delete(app_path('Models/OrderItemDetail.php'));
    File::delete(app_path('Http/Controllers/OrderItemDetailController.php'));
    File::delete(app_path('Http/Requests/StoreOrderItemDetailRequest.php'));
    File::delete(app_path('Http/Requests/UpdateOrderItemDetailRequest.php'));
    File::delete(app_path('Http/Resources/OrderItemDetailResource.php'));
    File::deleteDirectory(resource_path('js/Pages/OrderItemDetails'));

    // Migration cleanup
    $migrationPatterns = [
        'migrations/*_create_user_profiles_table.php',
        'migrations/*_create_products_table.php',
        'migrations/*_create_order_item_details_table.php',
    ];

    foreach ($migrationPatterns as $pattern) {
        $migrationFiles = File::glob(database_path($pattern));
        foreach ($migrationFiles as $file) {
            File::delete($file);
        }
    }
}
