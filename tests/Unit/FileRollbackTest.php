<?php

use Necro304\CrudInertiaShadcn\Support\FileRollback;

describe('FileRollback', function () {
    beforeEach(function () {
        $this->testDir = sys_get_temp_dir() . '/crud_generator_test_' . uniqid();
        mkdir($this->testDir, 0755, true);
    });

    afterEach(function () {
        // Cleanup test directory
        if (is_dir($this->testDir)) {
            array_map('unlink', glob("{$this->testDir}/*"));
            rmdir($this->testDir);
        }
    });

    test('tracks created file', function () {
        $rollback = new FileRollback;
        $filePath = $this->testDir . '/test.txt';

        file_put_contents($filePath, 'test content');
        $rollback->track($filePath);

        expect(file_exists($filePath))->toBeTrue();
    });

    test('rollback deletes tracked files', function () {
        $rollback = new FileRollback;
        $filePath = $this->testDir . '/test.txt';

        file_put_contents($filePath, 'test content');
        $rollback->track($filePath);
        $rollback->rollback();

        expect(file_exists($filePath))->toBeFalse();
    });

    test('rollback handles multiple files', function () {
        $rollback = new FileRollback;
        $file1 = $this->testDir . '/file1.txt';
        $file2 = $this->testDir . '/file2.txt';
        $file3 = $this->testDir . '/file3.txt';

        file_put_contents($file1, 'content 1');
        file_put_contents($file2, 'content 2');
        file_put_contents($file3, 'content 3');

        $rollback->track($file1);
        $rollback->track($file2);
        $rollback->track($file3);

        $rollback->rollback();

        expect(file_exists($file1))->toBeFalse();
        expect(file_exists($file2))->toBeFalse();
        expect(file_exists($file3))->toBeFalse();
    });

    test('commit prevents rollback deletion', function () {
        $rollback = new FileRollback;
        $filePath = $this->testDir . '/test.txt';

        file_put_contents($filePath, 'test content');
        $rollback->track($filePath);
        $rollback->commit();
        $rollback->rollback();

        expect(file_exists($filePath))->toBeTrue();
    });

    test('rollback is safe when file already deleted', function () {
        $rollback = new FileRollback;
        $filePath = $this->testDir . '/test.txt';

        file_put_contents($filePath, 'test content');
        $rollback->track($filePath);
        unlink($filePath); // Delete manually

        $rollback->rollback(); // Should not throw exception

        expect(file_exists($filePath))->toBeFalse();
    });

    test('rollback deletes in reverse order', function () {
        $rollback = new FileRollback;
        $deletionOrder = [];

        // Track files in specific order
        foreach (['file1.txt', 'file2.txt', 'file3.txt'] as $filename) {
            $filePath = $this->testDir . "/{$filename}";
            file_put_contents($filePath, 'content');
            $rollback->track($filePath);
        }

        // Mock deletion to track order
        $files = glob("{$this->testDir}/*");
        expect($files)->toHaveCount(3);

        $rollback->rollback();

        // Verify all files deleted
        expect(glob("{$this->testDir}/*"))->toHaveCount(0);
    });

    test('can track and rollback after previous commit', function () {
        $rollback = new FileRollback;

        // First batch
        $file1 = $this->testDir . '/file1.txt';
        file_put_contents($file1, 'content 1');
        $rollback->track($file1);
        $rollback->commit();

        // Second batch
        $file2 = $this->testDir . '/file2.txt';
        file_put_contents($file2, 'content 2');
        $rollback->track($file2);
        $rollback->rollback();

        expect(file_exists($file1))->toBeTrue(); // Committed, should exist
        expect(file_exists($file2))->toBeFalse(); // Rolled back, should not exist
    });

    test('reset clears tracked files without deleting', function () {
        $rollback = new FileRollback;
        $filePath = $this->testDir . '/test.txt';

        file_put_contents($filePath, 'test content');
        $rollback->track($filePath);
        $rollback->reset();
        $rollback->rollback();

        expect(file_exists($filePath))->toBeTrue(); // Should still exist after reset
    });

    test('throws exception when tracking non-existent file', function () {
        $rollback = new FileRollback;
        $rollback->track($this->testDir . '/non_existent.txt');
    })->throws(InvalidArgumentException::class);
});
