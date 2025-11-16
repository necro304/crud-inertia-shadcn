<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn\Support;

use Illuminate\Support\Str;

class NamingConverter
{
    /**
     * Convert snake_case to PascalCase
     */
    public static function toPascalCase(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return Str::studly($value);
    }

    /**
     * Convert PascalCase to snake_case
     */
    public static function toSnakeCase(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return Str::snake($value);
    }

    /**
     * Convert snake_case to kebab-case
     */
    public static function toKebabCase(string $value): string
    {
        if ($value === '') {
            return '';
        }

        return str_replace('_', '-', $value);
    }

    /**
     * Convert singular to plural
     */
    public static function toPlural(string $value): string
    {
        return Str::plural($value);
    }

    /**
     * Convert PascalCase model name to snake_case plural table name
     */
    public static function toTableName(string $modelName): string
    {
        $snakeCase = self::toSnakeCase($modelName);

        return self::toPlural($snakeCase);
    }

    /**
     * Convert PascalCase model name to kebab-case plural route name
     */
    public static function toRouteName(string $modelName): string
    {
        $snakeCase = self::toSnakeCase($modelName);
        $plural = self::toPlural($snakeCase);

        return self::toKebabCase($plural);
    }
}
