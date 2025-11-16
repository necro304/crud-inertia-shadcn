<?php

use Necro304\CrudInertiaShadcn\Parsers\ValidationRuleBuilder;

describe('ValidationRuleBuilder', function () {
    test('builds basic validation rules for string field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('name', 'string', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('string');
        expect($rules)->toContain('max:255');
    });

    test('builds validation rules for text field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('description', 'text', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('string');
    });

    test('builds validation rules for integer field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('quantity', 'integer', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('integer');
    });

    test('builds validation rules for decimal field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('price', 'decimal', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('numeric');
    });

    test('builds validation rules for boolean field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('is_active', 'boolean', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('boolean');
    });

    test('builds validation rules for date field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('birth_date', 'date', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('date');
    });

    test('builds validation rules for datetime field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('created_at', 'datetime', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('date');
    });

    test('builds validation rules for timestamp field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('updated_at', 'timestamp', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('date');
    });

    test('builds validation rules for json field', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('metadata', 'json', []);

        expect($rules)->toContain('required');
        expect($rules)->toContain('json');
    });

    test('adds nullable when nullable modifier present', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('email', 'string', ['nullable']);

        expect($rules)->toContain('nullable');
        expect($rules)->toContain('string');
        expect($rules)->not->toContain('required');
    });

    test('adds unique when unique modifier present', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('slug', 'string', ['unique']);

        expect($rules)->toContain('required');
        expect($rules)->toContain('string');
        expect($rules)->toContain('unique:table_name,slug');
    });

    test('combines nullable and unique modifiers', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->build('email', 'string', ['nullable', 'unique']);

        expect($rules)->toContain('nullable');
        expect($rules)->toContain('string');
        expect($rules)->toContain('unique:table_name,email');
        expect($rules)->not->toContain('required');
    });

    test('builds update rules with unique ignore for current record', function () {
        $builder = new ValidationRuleBuilder;
        $rules = $builder->buildForUpdate('email', 'string', ['unique'], 'users', 1);

        expect($rules)->toContain('required');
        expect($rules)->toContain('string');
        expect($rules)->toContain('unique:users,email,1');
    });

    test('throws exception for unsupported field type', function () {
        $builder = new ValidationRuleBuilder;
        $builder->build('field', 'unsupported_type', []);
    })->throws(InvalidArgumentException::class);
});
