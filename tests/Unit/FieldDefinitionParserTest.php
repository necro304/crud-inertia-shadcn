<?php

use Isaac\CrudGenerator\Parsers\FieldDefinitionParser;

describe('FieldDefinitionParser', function () {
    test('parses basic field definition without modifiers', function () {
        $parser = new FieldDefinitionParser();
        $field = $parser->parse('name:string');

        expect($field)->toHaveKeys(['name', 'type', 'modifiers']);
        expect($field['name'])->toBe('name');
        expect($field['type'])->toBe('string');
        expect($field['modifiers'])->toBeEmpty();
    });

    test('parses field definition with nullable modifier', function () {
        $parser = new FieldDefinitionParser();
        $field = $parser->parse('email:string:nullable');

        expect($field['name'])->toBe('email');
        expect($field['type'])->toBe('string');
        expect($field['modifiers'])->toContain('nullable');
    });

    test('parses field definition with unique modifier', function () {
        $parser = new FieldDefinitionParser();
        $field = $parser->parse('slug:string:unique');

        expect($field['name'])->toBe('slug');
        expect($field['type'])->toBe('string');
        expect($field['modifiers'])->toContain('unique');
    });

    test('parses field definition with combined modifiers', function () {
        $parser = new FieldDefinitionParser();
        $field = $parser->parse('email:string:nullable:unique');

        expect($field['name'])->toBe('email');
        expect($field['type'])->toBe('string');
        expect($field['modifiers'])->toContain('nullable');
        expect($field['modifiers'])->toContain('unique');
        expect($field['modifiers'])->toHaveCount(2);
    });

    test('supports all required field types', function () {
        $parser = new FieldDefinitionParser();
        $types = ['string', 'text', 'integer', 'decimal', 'boolean', 'date', 'datetime', 'timestamp', 'json'];

        foreach ($types as $type) {
            $field = $parser->parse("field:{$type}");
            expect($field['type'])->toBe($type);
        }
    });

    test('throws exception for invalid field definition format', function () {
        $parser = new FieldDefinitionParser();
        $parser->parse('invalid_field');
    })->throws(InvalidArgumentException::class);

    test('throws exception for invalid field type', function () {
        $parser = new FieldDefinitionParser();
        $parser->parse('field:invalid_type');
    })->throws(InvalidArgumentException::class);

    test('throws exception for invalid modifier', function () {
        $parser = new FieldDefinitionParser();
        $parser->parse('field:string:invalid_modifier');
    })->throws(InvalidArgumentException::class);

    test('throws exception for empty field name', function () {
        $parser = new FieldDefinitionParser();
        $parser->parse(':string');
    })->throws(InvalidArgumentException::class);

    test('parses multiple field definitions correctly', function () {
        $parser = new FieldDefinitionParser();
        $fields = [
            'name:string',
            'price:decimal:nullable',
            'published:boolean',
            'description:text:nullable',
        ];

        foreach ($fields as $definition) {
            $field = $parser->parse($definition);
            expect($field)->toHaveKeys(['name', 'type', 'modifiers']);
        }
    });
});
