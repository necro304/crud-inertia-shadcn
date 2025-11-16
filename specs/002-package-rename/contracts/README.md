# API Contracts

## Package Rename Feature

**Status**: No API Contracts Required

This feature is a package metadata rename with **ZERO API surface area**. There are no:
- HTTP endpoints
- REST APIs
- GraphQL schemas
- Public methods changing signatures
- Service contracts
- Interface definitions

## Public API Stability

The package rename preserves 100% backward compatibility with the existing public API:

### Service Provider Registration
```php
// Before and After - UNCHANGED
'providers' => [
    Necro304\CrudInertiaShadcn\CrudGeneratorServiceProvider::class,
],
```

### Artisan Commands
```bash
# Before and After - UNCHANGED
php artisan crud:generate Company
php artisan crud:model Company
php artisan crud:controller CompanyController
```

### Facade Usage
```php
// Before and After - UNCHANGED (if facades exist)
use Necro304\CrudInertiaShadcn\Facades\CrudGenerator;

CrudGenerator::generate('Company');
```

### Configuration API
```php
// Before and After - UNCHANGED
config('crud-generator.models_path');
config('crud-generator.controllers_path');
```

## Breaking Change Analysis

**Breaking Changes**: ZERO

**Reason**: Package name and namespace changes do NOT affect:
- Method signatures
- Class names (beyond namespace)
- Configuration keys
- Published asset paths
- Artisan command signatures
- Event names
- Service container bindings

## Installation Contract

The ONLY user-facing change is the composer require command:

**Before** (placeholder):
```bash
composer require :vendor_slug/:package_slug
```

**After** (actual):
```bash
composer require necro304/crud-inertia-shadcn
```

**Migration Path for Existing Users**: Update composer.json `require` section and run `composer update`.

## Summary

This feature has no API contracts document because it changes package metadata, not public API behavior. All existing functionality remains 100% compatible.
