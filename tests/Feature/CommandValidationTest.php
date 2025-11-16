<?php

use Necro304\CrudInertiaShadcn\Commands\MakeCrudCommand;

test('rejects invalid resource name with special characters', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product@#$',
        'fields' => ['name:string'],
    ])->assertFailed();
});

test('rejects resource name starting with number', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => '123Product',
        'fields' => ['name:string'],
    ])->assertFailed();
});

test('rejects resource name exceeding 50 characters', function () {
    $longName = str_repeat('A', 51);

    $this->artisan(MakeCrudCommand::class, [
        'resource' => $longName,
        'fields' => ['name:string'],
    ])->assertFailed();
});

test('accepts resource name with 50 characters', function () {
    $name = str_repeat('A', 50);

    $this->artisan(MakeCrudCommand::class, [
        'resource' => $name,
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // Cleanup
    \Illuminate\Support\Facades\File::delete(app_path("Models/{$name}.php"));
    \Illuminate\Support\Facades\File::delete(app_path("Http/Controllers/{$name}Controller.php"));
    \Illuminate\Support\Facades\File::delete(app_path("Http/Requests/Store{$name}Request.php"));
    \Illuminate\Support\Facades\File::delete(app_path("Http/Requests/Update{$name}Request.php"));
    \Illuminate\Support\Facades\File::delete(app_path("Http/Resources/{$name}Resource.php"));
    \Illuminate\Support\Facades\File::deleteDirectory(resource_path("js/Pages/{$name}s"));

    $tableName = \Illuminate\Support\Str::snake(\Illuminate\Support\Str::plural($name));
    $migrationFiles = \Illuminate\Support\Facades\File::glob(database_path("migrations/*_create_{$tableName}_table.php"));
    foreach ($migrationFiles as $file) {
        \Illuminate\Support\Facades\File::delete($file);
    }
});

test('rejects reserved word as resource name', function () {
    $reservedWords = ['class', 'function', 'namespace', 'return', 'interface'];

    foreach ($reservedWords as $word) {
        $this->artisan(MakeCrudCommand::class, [
            'resource' => $word,
            'fields' => ['name:string'],
        ])->assertFailed();
    }
});

test('rejects invalid field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:invalid_type'],
    ])->assertFailed();
});

test('rejects invalid field definition format', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['invalid_field_without_type'],
    ])->assertFailed();
});

test('rejects invalid field modifier', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:string:invalid_modifier'],
    ])->assertFailed();
});

test('rejects empty resource name', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => '',
        'fields' => ['name:string'],
    ])->assertFailed();
});

test('rejects when no fields provided', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => [],
    ])->assertFailed();
});

test('accepts valid resource name with underscores', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'User_Profile',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // Cleanup
    \Illuminate\Support\Facades\File::delete(app_path('Models/User_Profile.php'));
    \Illuminate\Support\Facades\File::delete(app_path('Http/Controllers/User_ProfileController.php'));
    \Illuminate\Support\Facades\File::delete(app_path('Http/Requests/StoreUser_ProfileRequest.php'));
    \Illuminate\Support\Facades\File::delete(app_path('Http/Requests/UpdateUser_ProfileRequest.php'));
    \Illuminate\Support\Facades\File::delete(app_path('Http/Resources/User_ProfileResource.php'));
    \Illuminate\Support\Facades\File::deleteDirectory(resource_path('js/Pages/User_Profiles'));

    $migrationFiles = \Illuminate\Support\Facades\File::glob(database_path('migrations/*_create_user__profiles_table.php'));
    foreach ($migrationFiles as $file) {
        \Illuminate\Support\Facades\File::delete($file);
    }
});

test('validates multiple field definitions', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => [
            'name:string',
            'price:invalid',  // Invalid type
            'quantity:integer',
        ],
    ])->assertFailed();
});

test('provides helpful error message for invalid field type', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product',
        'fields' => ['name:varchar'],
    ])
        ->assertFailed()
        ->expectsOutputToContain('Invalid field type: varchar');
});

test('provides helpful error message for reserved word', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Class',
        'fields' => ['name:string'],
    ])
        ->assertFailed()
        ->expectsOutputToContain('reserved word');
});

test('accepts alphanumeric resource name', function () {
    $this->artisan(MakeCrudCommand::class, [
        'resource' => 'Product123',
        'fields' => ['name:string'],
    ])->assertSuccessful();

    // Cleanup
    \Illuminate\Support\Facades\File::delete(app_path('Models/Product123.php'));
    \Illuminate\Support\Facades\File::delete(app_path('Http/Controllers/Product123Controller.php'));
    \Illuminate\Support\Facades\File::delete(app_path('Http/Requests/StoreProduct123Request.php'));
    \Illuminate\Support\Facades\File::delete(app_path('Http/Requests/UpdateProduct123Request.php'));
    \Illuminate\Support\Facades\File::delete(app_path('Http/Resources/Product123Resource.php'));
    \Illuminate\Support\Facades\File::deleteDirectory(resource_path('js/Pages/Product123s'));

    $migrationFiles = \Illuminate\Support\Facades\File::glob(database_path('migrations/*_create_product123s_table.php'));
    foreach ($migrationFiles as $file) {
        \Illuminate\Support\Facades\File::delete($file);
    }
});
