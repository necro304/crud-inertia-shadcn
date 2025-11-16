<?php

declare(strict_types=1);

namespace Necro304\CrudInertiaShadcn\Parsers;

use InvalidArgumentException;

class ValidationRuleBuilder
{
    /**
     * Field type to validation rule mappings
     */
    private const TYPE_RULES = [
        'string' => ['string', 'max:255'],
        'text' => ['string'],
        'integer' => ['integer'],
        'decimal' => ['numeric'],
        'boolean' => ['boolean'],
        'date' => ['date'],
        'datetime' => ['date'],
        'timestamp' => ['date'],
        'json' => ['json'],
    ];

    /**
     * Build validation rules for a field
     *
     * @param string $name Field name
     * @param string $type Field type
     * @param array<string> $modifiers Field modifiers
     *
     * @return array<string>
     *
     * @throws InvalidArgumentException
     */
    public function build(string $name, string $type, array $modifiers): array
    {
        if (! isset(self::TYPE_RULES[$type])) {
            throw new InvalidArgumentException("Unsupported field type: {$type}");
        }

        $rules = [];

        // Add required or nullable
        if (in_array('nullable', $modifiers, true)) {
            $rules[] = 'nullable';
        } else {
            $rules[] = 'required';
        }

        // Add type-specific rules
        $rules = array_merge($rules, self::TYPE_RULES[$type]);

        // Add unique rule if modifier present
        if (in_array('unique', $modifiers, true)) {
            $rules[] = "unique:table_name,{$name}";
        }

        return $rules;
    }

    /**
     * Build validation rules for update operations
     *
     * @param string $name Field name
     * @param string $type Field type
     * @param array<string> $modifiers Field modifiers
     * @param string $tableName Table name for unique rule
     * @param int|string $ignoreId ID to ignore in unique validation
     *
     * @return array<string>
     *
     * @throws InvalidArgumentException
     */
    public function buildForUpdate(string $name, string $type, array $modifiers, string $tableName, int|string $ignoreId): array
    {
        $rules = $this->build($name, $type, $modifiers);

        // Update unique rule to include ignore clause
        foreach ($rules as $key => $rule) {
            if (str_starts_with($rule, 'unique:')) {
                $rules[$key] = "unique:{$tableName},{$name},{$ignoreId}";
            }
        }

        return $rules;
    }
}
