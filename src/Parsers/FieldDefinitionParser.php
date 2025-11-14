<?php

declare(strict_types=1);

namespace Isaac\CrudGenerator\Parsers;

use InvalidArgumentException;

class FieldDefinitionParser
{
    /**
     * Supported field types
     */
    private const VALID_TYPES = [
        'string',
        'text',
        'integer',
        'decimal',
        'boolean',
        'date',
        'datetime',
        'timestamp',
        'json',
    ];

    /**
     * Supported field modifiers
     */
    private const VALID_MODIFIERS = [
        'nullable',
        'unique',
    ];

    /**
     * Parse field definition string into structured array
     *
     * @param  string  $definition  Format: name:type or name:type:modifier1:modifier2
     * @return array{name: string, type: string, modifiers: array<string>}
     *
     * @throws InvalidArgumentException
     */
    public function parse(string $definition): array
    {
        // Validate format using regex
        if (! preg_match('/^[a-z_]+:[a-z]+(?::[a-z]+)*$/', $definition)) {
            throw new InvalidArgumentException("Invalid field definition format: {$definition}. Expected format: name:type or name:type:modifier");
        }

        $parts = explode(':', $definition);

        // Extract name (first part)
        $name = array_shift($parts);

        if (empty($name)) {
            throw new InvalidArgumentException('Field name cannot be empty');
        }

        // Extract type (second part)
        $type = array_shift($parts);

        if (! in_array($type, self::VALID_TYPES, true)) {
            throw new InvalidArgumentException("Invalid field type: {$type}. Supported types: " . implode(', ', self::VALID_TYPES));
        }

        // Extract modifiers (remaining parts)
        $modifiers = $parts;

        // Validate modifiers
        foreach ($modifiers as $modifier) {
            if (! in_array($modifier, self::VALID_MODIFIERS, true)) {
                throw new InvalidArgumentException("Invalid modifier: {$modifier}. Supported modifiers: " . implode(', ', self::VALID_MODIFIERS));
            }
        }

        return [
            'name' => $name,
            'type' => $type,
            'modifiers' => $modifiers,
        ];
    }
}
