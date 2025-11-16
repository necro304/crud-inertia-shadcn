<?php

use Necro304\CrudInertiaShadcn\Support\NamingConverter;

describe('NamingConverter', function () {
    test('toPascalCase converts snake_case to PascalCase', function () {
        expect(NamingConverter::toPascalCase('user_profile'))->toBe('UserProfile');
        expect(NamingConverter::toPascalCase('product'))->toBe('Product');
        expect(NamingConverter::toPascalCase('order_item_detail'))->toBe('OrderItemDetail');
    });

    test('toSnakeCase converts PascalCase to snake_case', function () {
        expect(NamingConverter::toSnakeCase('UserProfile'))->toBe('user_profile');
        expect(NamingConverter::toSnakeCase('Product'))->toBe('product');
        expect(NamingConverter::toSnakeCase('OrderItemDetail'))->toBe('order_item_detail');
    });

    test('toKebabCase converts snake_case to kebab-case', function () {
        expect(NamingConverter::toKebabCase('user_profile'))->toBe('user-profile');
        expect(NamingConverter::toKebabCase('product'))->toBe('product');
        expect(NamingConverter::toKebabCase('order_item_detail'))->toBe('order-item-detail');
    });

    test('toPlural converts singular to plural correctly', function () {
        expect(NamingConverter::toPlural('user'))->toBe('users');
        expect(NamingConverter::toPlural('category'))->toBe('categories');
        expect(NamingConverter::toPlural('product'))->toBe('products');
        expect(NamingConverter::toPlural('person'))->toBe('people');
    });

    test('toTableName converts PascalCase model to snake_case plural table', function () {
        expect(NamingConverter::toTableName('User'))->toBe('users');
        expect(NamingConverter::toTableName('UserProfile'))->toBe('user_profiles');
        expect(NamingConverter::toTableName('Category'))->toBe('categories');
        expect(NamingConverter::toTableName('OrderItem'))->toBe('order_items');
    });

    test('toRouteName converts PascalCase to kebab-case plural route', function () {
        expect(NamingConverter::toRouteName('User'))->toBe('users');
        expect(NamingConverter::toRouteName('UserProfile'))->toBe('user-profiles');
        expect(NamingConverter::toRouteName('Product'))->toBe('products');
        expect(NamingConverter::toRouteName('OrderItem'))->toBe('order-items');
    });

    test('handles empty strings gracefully', function () {
        expect(NamingConverter::toPascalCase(''))->toBe('');
        expect(NamingConverter::toSnakeCase(''))->toBe('');
        expect(NamingConverter::toKebabCase(''))->toBe('');
    });

    test('handles single word correctly across all methods', function () {
        expect(NamingConverter::toPascalCase('product'))->toBe('Product');
        expect(NamingConverter::toSnakeCase('Product'))->toBe('product');
        expect(NamingConverter::toKebabCase('product'))->toBe('product');
        expect(NamingConverter::toPlural('product'))->toBe('products');
    });
});
