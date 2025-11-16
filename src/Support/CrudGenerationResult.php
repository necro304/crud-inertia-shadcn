<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Support;

final class CrudGenerationResult
{
    /**
     * @param array<string, string|array<string, string>> $files
     * @param array<string, mixed> $options
     */
    public function __construct(
        public readonly string $resourceName,
        private array $files,
        public readonly array $options,
    ) {}

    /**
     * @return array<string, string|array<string, string>>
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * @return array<string, string>
     */
    public function flatFiles(): array
    {
        $flattened = [];

        foreach ($this->files as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subKey => $path) {
                    $flattened[sprintf('%s.%s', $key, $subKey)] = $path;
                }

                continue;
            }

            $flattened[$key] = $value;
        }

        return $flattened;
    }

    public function fileCount(): int
    {
        return count($this->flatFiles());
    }

    public function generatedViews(): bool
    {
        return (bool) ($this->options['generate_views'] ?? false);
    }
}
