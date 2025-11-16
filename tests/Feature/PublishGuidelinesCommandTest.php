<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;

it('publishes guidelines file to .ai/guidelines directory', function () {
    $targetPath = base_path('.ai/guidelines/crud-inertia-shadcn.md');

    // Ensure file doesn't exist before test
    if (file_exists($targetPath)) {
        unlink($targetPath);
    }

    // Run the command
    $this->artisan('crud-generator:publish-guidelines')
        ->assertSuccessful()
        ->expectsOutput('✓ Guidelines published successfully!');

    // Assert file was created
    expect($targetPath)->toBeFile();

    // Assert file contains expected content
    $content = file_get_contents($targetPath);
    expect($content)
        ->toContain('CRUD Inertia Shadcn Package Guidelines')
        ->toContain('necro304/crud-inertia-shadcn')
        ->toContain('php artisan make:crud');

    // Clean up
    unlink($targetPath);
    if (is_dir(dirname($targetPath))) {
        rmdir(dirname($targetPath));
    }
    if (is_dir(base_path('.ai'))) {
        rmdir(base_path('.ai'));
    }
});

it('does not overwrite existing guidelines without force flag', function () {
    $targetPath = base_path('.ai/guidelines/crud-inertia-shadcn.md');
    $targetDir = dirname($targetPath);

    // Create directory and file
    if (! is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    file_put_contents($targetPath, 'Existing content');

    // Run command without force flag
    $this->artisan('crud-generator:publish-guidelines')
        ->assertFailed()
        ->expectsOutput('Guidelines file already exists!');

    // Assert file still has original content
    expect(file_get_contents($targetPath))->toBe('Existing content');

    // Clean up
    unlink($targetPath);
    rmdir(dirname($targetPath));
    if (is_dir(base_path('.ai'))) {
        rmdir(base_path('.ai'));
    }
});

it('overwrites existing guidelines with force flag', function () {
    $targetPath = base_path('.ai/guidelines/crud-inertia-shadcn.md');
    $targetDir = dirname($targetPath);

    // Create directory and file
    if (! is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    file_put_contents($targetPath, 'Old content');

    // Run command with force flag
    $this->artisan('crud-generator:publish-guidelines --force')
        ->assertSuccessful()
        ->expectsOutput('✓ Guidelines published successfully!');

    // Assert file has new content
    $content = file_get_contents($targetPath);
    expect($content)
        ->not->toBe('Old content')
        ->toContain('CRUD Inertia Shadcn Package Guidelines');

    // Clean up
    unlink($targetPath);
    rmdir(dirname($targetPath));
    if (is_dir(base_path('.ai'))) {
        rmdir(base_path('.ai'));
    }
});

it('creates .ai/guidelines directory if it does not exist', function () {
    $targetPath = base_path('.ai/guidelines/crud-inertia-shadcn.md');
    $targetDir = dirname($targetPath);

    // Ensure directory doesn't exist
    if (is_dir($targetDir)) {
        if (file_exists($targetPath)) {
            unlink($targetPath);
        }
        rmdir($targetDir);
    }
    if (is_dir(base_path('.ai'))) {
        rmdir(base_path('.ai'));
    }

    // Run the command
    $this->artisan('crud-generator:publish-guidelines')
        ->assertSuccessful();

    // Assert directory was created
    expect($targetDir)->toBeDirectory();
    expect($targetPath)->toBeFile();

    // Clean up
    unlink($targetPath);
    rmdir(dirname($targetPath));
    rmdir(base_path('.ai'));
});
