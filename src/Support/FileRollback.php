<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn\Support;

use InvalidArgumentException;

class FileRollback
{
    /**
     * List of tracked file paths
     *
     * @var array<string>
     */
    private array $trackedFiles = [];

    /**
     * Whether the transaction is committed
     */
    private bool $committed = false;

    /**
     * Track a file for potential rollback
     *
     * @throws InvalidArgumentException
     */
    public function track(string $filePath): void
    {
        if (! file_exists($filePath)) {
            throw new InvalidArgumentException("Cannot track non-existent file: {$filePath}");
        }

        // Reset committed flag when starting to track new files
        if (empty($this->trackedFiles)) {
            $this->committed = false;
        }

        $this->trackedFiles[] = $filePath;
    }

    /**
     * Rollback by deleting all tracked files
     */
    public function rollback(): void
    {
        if ($this->committed) {
            return;
        }

        // Delete in reverse order (LIFO)
        foreach (array_reverse($this->trackedFiles) as $filePath) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        $this->trackedFiles = [];
    }

    /**
     * Commit the transaction, preventing rollback
     */
    public function commit(): void
    {
        $this->committed = true;
        $this->trackedFiles = [];
    }

    /**
     * Reset tracked files without deleting
     */
    public function reset(): void
    {
        $this->trackedFiles = [];
        $this->committed = false;
    }
}
