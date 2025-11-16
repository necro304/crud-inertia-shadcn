<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn\Support;

class StubRenderer
{
    /**
     * Render a stub template with token replacements
     *
     * @param string $template Stub template content
     * @param array<string, string> $tokens Token replacements
     */
    public function render(string $template, array $tokens): string
    {
        $result = $template;

        foreach ($tokens as $key => $value) {
            $placeholder = '{{ ' . $key . ' }}';
            $result = str_replace($placeholder, $value, $result);
        }

        return $result;
    }

    /**
     * Load stub from file and render with tokens
     *
     * @param string $stubPath Path to stub file
     * @param array<string, string> $tokens Token replacements
     */
    public function renderFromFile(string $stubPath, array $tokens): string
    {
        if (! file_exists($stubPath)) {
            throw new \InvalidArgumentException("Stub file not found: {$stubPath}");
        }

        $template = file_get_contents($stubPath);

        return $this->render($template, $tokens);
    }
}
