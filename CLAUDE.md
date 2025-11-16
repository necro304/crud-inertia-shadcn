# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel Package necro304/crud-inertia-shadcn** designed for creating Laravel packages with a focus on CRUD functionality using Inertia.js, Vue 3, and Shadcn UI. The repository contains both the package scaffold and example implementations demonstrating best practices for building full-stack CRUD applications.

## Package Configuration

**Initial Setup**: Before using this package as a template, run the configuration script to customize placeholders:

```bash
php ./configure.php
```

This interactive script replaces placeholders throughout the codebase with your actual package details.

## Development Commands

### Testing

```bash
# Run all tests with Pest
composer test

# Run tests with coverage
composer test-coverage

# Run a single test file
vendor/bin/pest tests/ExampleTest.php

# Run tests with filters
vendor/bin/pest --filter=test_name
```

### Code Quality

```bash
# Run PHPStan static analysis
composer analyse

# Fix code style with Laravel Pint
composer format

# Check for architectural violations (Pest Arch)
vendor/bin/pest --filter=arch
```

### Package Discovery

```bash
# Discover packages (runs automatically after composer install)
composer run prepare
```

## Architecture & Stack

### Backend (Laravel Package)

**Service Provider Pattern**: The package uses `Spatie\LaravelPackageTools\PackageServiceProvider` for bootstrapping:
- `crud-generatorServiceProvider.php` - Main service provider
- Supports: config files, views, migrations, and Artisan commands

**Example CRUD Structure** (in `example-files/`):
- **Models**: Eloquent models with SoftDeletes, Auditing, relationships, and query scopes
- **Controllers**: RESTful controllers using Inertia.js for SSR
- **Requests**: Form requests with validation rules (Store/Update patterns)
- **Resources**: API resources for transforming Eloquent models to JSON

### Frontend (Vue 3 + Inertia.js + Shadcn UI)

**Stack Components**:
- **Vue 3**: Composition API with TypeScript
- **Inertia.js**: Server-driven SPA framework
- **Shadcn UI**: Headless UI components (Button, Input, Badge, Select, AlertDialog, etc.)
- **TanStack Table**: Data tables with sorting, filtering, pagination
- **VueUse**: Composables library (e.g., `useDebounceFn` for search)
- **Lucide Icons**: Icon library (Plus, X, Filter, Building2, etc.)

**Key Frontend Patterns**:
- `useCrudColumns` composable for consistent table column definitions
- `DataTable` component for standardized data table UIs
- Wayfinder actions for controller method imports
- TypeScript interfaces for type safety (`Company`, `PaginatedResponse`, `BreadcrumbItem`)

### Key Laravel Packages Used

- **Spatie Query Builder**: Advanced filtering, sorting, and pagination (`AllowedFilter`, `QueryBuilder`)
- **Owen-It Auditing**: Model auditing for change tracking
- **Spatie Laravel Ray**: Debugging tool (dev dependency)

## CRUD Implementation Pattern

When creating new CRUD modules following the example pattern:

1. **Model** (`app/Models/`):
   - Implement `SoftDeletes` for soft deletion
   - Implement `Auditable` contract for change tracking
   - Define relationships (`HasMany`, `MorphMany`, etc.)
   - Add query scopes for common queries
   - Use `protected function casts()` method for type casting

2. **Controller** (`app/Http/Controllers/`):
   - Use `QueryBuilder::for()` with allowed filters and sorts
   - Implement search with callback filters for multiple fields
   - Load relationships with `with()` for N+1 prevention
   - Return Inertia responses with resources
   - Handle file uploads (e.g., logos) with storage disk

3. **Requests** (`app/Http/Requests/[Resource]/`):
   - Separate `StoreRequest` and `UpdateRequest`
   - Define validation rules in `rules()` method
   - Add custom authorization logic if needed

4. **Resources** (`app/Http/Resources/`):
   - Transform model data for API/frontend consumption
   - Include relationships conditionally

5. **Vue Pages** (`resources/js/pages/[resource]/`):
   - `Index.vue`: List view with DataTable, filtering, search, sorting
   - `Create.vue`: Creation form with CompanyForm component
   - `Edit.vue`: Edit form with pre-filled data
   - `Show.vue`: Detail view with related data
   - `components/[Resource]Form.vue`: Reusable form component for create/edit

## Testing Structure

### Pest PHP

Tests use Pest PHP with the following structure:
- `tests/Pest.php`: Global test configuration
- `tests/TestCase.php`: Base test case with Orchestra Testbench
- `tests/ArchTest.php`: Architectural tests (no debug functions: `dd`, `dump`, `ray`)
- `tests/ExampleTest.php`: Example test cases

**Architecture Tests**: The codebase enforces no debugging functions in production code via Pest Arch tests.

## Code Quality Standards

- **PHPStan Level**: 5 (analysis on `src/`, `config/`, `database/`)
- **Octane Compatibility**: Checked via PHPStan
- **Model Properties**: Validated via PHPStan
- **Code Style**: Laravel Pint (PSR-12 based)
- **PHP Version**: ^8.4 (also compatible with 8.3)
- **Laravel Version**: 12.*
- **Frontend Stack**: Vue 3 + TypeScript + Inertia.js + Shadcn-vue + Tailwind CSS 4

## File Upload Pattern

When handling file uploads (see `CompanyController` example):
1. Check for file presence with `hasFile()`
2. Store in organized paths: `[resource]/[type]` (e.g., `companies/logos`)
3. Use `public` disk for web-accessible files
4. Delete old files on update: `\Storage::disk('public')->delete($old)`

## Query Builder Filtering Pattern

Standard filtering implementation:
```php
QueryBuilder::for(Model::class)
    ->allowedFilters([
        AllowedFilter::callback('search', fn($q, $v) => /* multi-field search */),
        AllowedFilter::partial('field_name'),
        AllowedFilter::exact('status_field'),
    ])
    ->allowedSorts(['field1', 'field2', 'created_at'])
    ->defaultSort('-created_at')
    ->with(['relationship'])
    ->paginate(15)
    ->withQueryString();
```

## Frontend Data Table Pattern

Standard Vue 3 data table implementation:
- Use `DataTable` component with `ColumnDef` type definitions
- Implement debounced search with `useDebounceFn`
- Use `router.visit()` for filter updates with query string preservation
- Show active filters with badges and clear functionality
- Implement AlertDialog for delete confirmations

## Important Notes

- **Soft Deletes**: Always use soft deletes for models to maintain audit trails
- **Auditing**: Enable auditing on models that require change tracking
- **Relationships**: Eager load relationships to avoid N+1 queries
- **TypeScript**: Frontend uses strict TypeScript typing for all props and interfaces
- **Inertia**: All backend responses use `Inertia::render()` for SSR
- **Validation**: Always use Form Requests, never validate in controllers

## Active Technologies
- PHP ^8.4 (compatible with 8.3) (001-crud-generator-package)
- Files (stub templates, publishable assets) + Laravel's filesystem abstraction (001-crud-generator-package)
- PHP ^8.4 (8.3 compatible) + Laravel 12.*, Spatie Laravel Package Tools, Composer for package managemen (002-package-rename)
- File-based (composer.json, configuration files, documentation) (002-package-rename)

## Recent Changes
- 002-package-rename: Renamed package from placeholder to `necro304/crud-inertia-shadcn` with namespace `Necro304\CrudInertiaShadcn`
- 001-crud-generator-package: Added PHP ^8.4 (compatible with 8.3)
