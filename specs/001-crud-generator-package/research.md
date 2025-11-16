# Research: CRUD Generator Package

**Date**: 2025-01-13
**Feature**: 001-crud-generator-package
**Purpose**: Technical research for implementation decisions

## Decision 1: Laravel Package Foundation

**Decision**: Use Spatie Laravel Package Tools for package scaffolding

**Rationale**:
- Industry standard for Laravel package development (used by Spatie's 100+ packages)
- Provides proven service provider patterns with minimal boilerplate
- Built-in support for publishable assets (config, views, migrations, stubs)
- Orchestra Testbench integration for package testing
- Automatic command registration and asset publishing
- Well-documented and actively maintained

**Alternatives Considered**:
- **Manual package setup**: Rejected - requires extensive boilerplate, prone to configuration errors
- **Laravel's make:package (if existed)**: Not available in Laravel 12 core
- **Custom package isaac**: Rejected - reinventing proven patterns, maintenance overhead

**Implementation Notes**:
- Install: `composer require spatie/laravel-package-tools`
- Service provider extends `PackageServiceProvider` base class
- Use `Package` fluent API for configuration:
  ```php
  $package
      ->name('crud-generator')
      ->hasConfigFile()
      ->hasCommand(MakeCrudCommand::class)
      ->publishesServiceProvider('CrudGeneratorServiceProvider');
  ```

## Decision 2: Stub Template Strategy

**Decision**: Use Laravel's native stub system with custom publishable stubs

**Rationale**:
- Consistent with Laravel's own generator commands (make:model, make:controller)
- Developers already familiar with Laravel stub customization workflow
- Simple token replacement: `{{ PLACEHOLDER }}` syntax
- Supports vendor:publish for user customization
- No additional dependencies required
- Easy to maintain and test

**Alternatives Considered**:
- **Blade templates**: Rejected - overkill for simple token replacement, requires Blade compiler
- **Twig templates**: Rejected - external dependency, unfamiliar to Laravel developers
- **Custom template engine**: Rejected - unnecessary complexity, maintenance burden
- **Hardcoded strings**: Rejected - not customizable, violates FR-012

**Implementation Notes**:
- Store stubs in `resources/stubs/` directory
- Publish with tag: `php artisan vendor:publish --tag=crud-generator-stubs`
- Token format: `{{ RESOURCE_NAME }}`, `{{ TABLE_NAME }}`, `{{ FIELDS }}`
- Use PHP's `str_replace()` for token substitution (fast, no regex overhead)
- Example tokens:
  - `{{ NAMESPACE }}` - App namespace
  - `{{ RESOURCE_NAME }}` - PascalCase (e.g., Product)
  - `{{ RESOURCE_PLURAL }}` - PascalCase plural (e.g., Products)
  - `{{ TABLE_NAME }}` - snake_case plural (e.g., products)
  - `{{ ROUTE_NAME }}` - kebab-case plural (e.g., products)
  - `{{ FIELDS }}` - Dynamic field list for migrations/forms

## Decision 3: Field Definition Parsing

**Decision**: Regex-based parser with strict validation

**Rationale**:
- Simple syntax: `name:type:modifier1:modifier2`
- Regex provides fast, reliable parsing for structured input
- Early validation prevents invalid field definitions
- Clear error messages for developer feedback
- Performance: <1ms per field definition

**Alternatives Considered**:
- **PEG parser (e.g., Parsley)**: Rejected - overkill for simple syntax, external dependency
- **Manual string splitting**: Rejected - error-prone for edge cases, harder to validate
- **AST parser**: Rejected - unnecessary complexity for simple key:value format

**Implementation Notes**:
- Regex pattern: `/^([a-z_][a-z0-9_]*):([a-z]+)(?::([a-z:]+))?$/i`
- Validation rules:
  - Field name: lowercase, starts with letter, alphanumeric + underscore
  - Field type: must match supported types (string, text, integer, decimal, boolean, date, datetime, timestamp, json)
  - Modifiers: nullable, unique (can be combined)
- Parse steps:
  1. Split on space for multiple fields
  2. Regex match each field definition
  3. Validate field type against whitelist
  4. Validate modifiers against allowed list
  5. Return FieldDefinition DTO with name, type, modifiers array

## Decision 4: Naming Convention Converter

**Decision**: Centralized NamingConverter utility class with Laravel's Str helper

**Rationale**:
- Single source of truth for all naming transformations
- Laravel's `Illuminate\Support\Str` provides proven pluralization logic
- Avoids duplication across generators
- Easy to test in isolation
- Consistent naming across all generated files

**Alternatives Considered**:
- **Decentralized conversion**: Rejected - duplicated logic, inconsistency risk
- **Custom pluralization**: Rejected - Laravel's pluralizer handles edge cases (child/children, person/people)
- **Doctrine Inflector**: Rejected - Laravel's Str is sufficient, no extra dependency needed

**Implementation Notes**:
- Methods:
  - `toPascalCase(string $name): string` - Product
  - `toCamelCase(string $name): string` - product
  - `toSnakeCase(string $name): string` - product
  - `toKebabCase(string $name): string` - product
  - `toPlural(string $name): string` - products
  - `toTableName(string $resource): string` - products (snake_case plural)
  - `toRouteName(string $resource): string` - products (kebab-case plural)
- Use Laravel's `Str::studly()`, `Str::snake()`, `Str::plural()` helpers

## Decision 5: Atomic Generation with Rollback

**Decision**: Transaction-like file operation tracking with try-catch rollback

**Rationale**:
- Prevents partial CRUD state (FR-017 requirement)
- Developer experience: all-or-nothing guarantee
- Simple implementation: track created files, delete on exception
- No database required (file-based tracking)
- Testable with mock filesystem

**Alternatives Considered**:
- **Git-based rollback**: Rejected - assumes git repository, complex, slow
- **Backup before generation**: Rejected - requires disk space, cleanup complexity
- **No rollback (manual cleanup)**: Rejected - violates FR-017, poor developer experience
- **Database transaction**: N/A - not applicable to file operations

**Implementation Notes**:
```php
class FileRollback {
    private array $createdFiles = [];

    public function track(string $path): void {
        $this->createdFiles[] = $path;
    }

    public function rollback(): void {
        foreach (array_reverse($this->createdFiles) as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
        $this->createdFiles = [];
    }

    public function commit(): void {
        $this->createdFiles = [];
    }
}

// Usage in MakeCrudCommand
try {
    $rollback = new FileRollback();

    $modelPath = $this->generateModel();
    $rollback->track($modelPath);

    $controllerPath = $this->generateController();
    $rollback->track($controllerPath);

    // ... generate all files

    $rollback->commit(); // Success
} catch (Exception $e) {
    $rollback->rollback(); // Clean up on failure
    throw $e;
}
```

## Decision 6: Validation Rule Generation

**Decision**: Type-based rule builder with modifier extensions

**Rationale**:
- Consistent validation across all generated CRUDs
- Type safety: each field type has appropriate validation
- Extensible: modifiers add additional rules
- Follows Laravel validation conventions
- Reduces manual validation errors

**Alternatives Considered**:
- **Hardcoded rules in stubs**: Rejected - not flexible, hard to maintain
- **Manual rule specification**: Rejected - error-prone, verbose for users
- **No validation generation**: Rejected - violates FR-006, poor quality

**Implementation Notes**:

Type mappings:
```php
'string'    => 'required|string|max:255',
'text'      => 'required|string',
'integer'   => 'required|integer',
'decimal'   => 'required|numeric',
'boolean'   => 'required|boolean',
'date'      => 'required|date',
'datetime'  => 'required|date',
'timestamp' => 'required|date',
'json'      => 'required|json',
```

Modifier adjustments:
- `:nullable` → Replace `required` with `nullable`
- `:unique` → Add `|unique:table_name,column_name`

Special case for UpdateRequest:
- Unique rules must ignore current record: `unique:table_name,column_name,{id}`

## Decision 7: Vue Component Generation Strategy

**Decision**: TypeScript-first generation with strict interfaces

**Rationale**:
- Constitution requirement: TypeScript strict mode (Principle IV)
- Type safety prevents runtime errors in generated components
- Better IDE support for developers customizing generated code
- Consistent with modern Vue 3 + TypeScript patterns
- Shadcn-vue components are TypeScript-native

**Alternatives Considered**:
- **JavaScript generation**: Rejected - violates Constitution Principle IV
- **Optional TypeScript**: Rejected - inconsistent quality, defeats type safety purpose
- **Separate TS/JS stubs**: Rejected - maintenance burden, fragmentation

**Implementation Notes**:

Generated TypeScript interfaces:
```typescript
// Resource interface (from backend Resource class)
interface {{ RESOURCE_NAME }}Resource {
  id: number;
  {{ FIELD_INTERFACES }}  // e.g., name: string; price: number;
  created_at: string;
  updated_at: string;
}

// Paginated response
interface PaginatedResponse<T> {
  data: T[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
}

// Index page props
interface IndexProps {
  {{ RESOURCE_PLURAL }}: PaginatedResponse<{{ RESOURCE_NAME }}Resource>;
  filters: Record<string, string>;
}
```

## Decision 8: Code Quality Assurance Strategy

**Decision**: Multi-layer validation during generation process

**Rationale**:
- Success Criterion SC-002: generated code must pass PHPStan Level 5 and Laravel Pint
- Catch quality issues during generation, not after
- Fast feedback loop for developers
- Automated quality gates prevent manual review burden

**Alternatives Considered**:
- **Post-generation validation only**: Rejected - slow feedback, poor developer experience
- **No validation**: Rejected - violates SC-002, produces broken code
- **Manual code review**: Rejected - not scalable, inconsistent

**Implementation Notes**:

Validation layers:
1. **Input validation** (command execution):
   - Resource name format check (alphanumeric, starts with letter)
   - Field definition syntax validation
   - Relationship target existence check

2. **Template validation** (stub rendering):
   - All required tokens present in stub files
   - Valid PHP/Vue syntax in stubs
   - No placeholder tokens remaining after rendering

3. **Output validation** (generated files):
   - PHP: Run `php -l` for syntax check
   - PHP: Optional PHPStan analysis if available (`--validate` flag)
   - Vue: Optional TypeScript check if tsc available (`--validate` flag)

4. **Integration testing** (package test suite):
   - Full generation scenarios run PHPStan on generated code
   - Full generation scenarios run Pint on generated code
   - Architectural tests prevent debugging functions

## Decision 9: Command Output Strategy

**Decision**: Symfony Console output with three verbosity levels

**Rationale**:
- Laravel commands use Symfony Console component
- Standard verbosity levels: normal, quiet, verbose
- Consistent with other Laravel/Artisan commands
- Supports CI/CD integration (--quiet for scripts)
- Debugging support (--verbose for troubleshooting)

**Alternatives Considered**:
- **Single output level**: Rejected - violates FR-018, not flexible
- **Custom logging system**: Rejected - unnecessary when Symfony Console provides this
- **Silent by default**: Rejected - poor developer experience, hard to debug

**Implementation Notes**:

Output levels (FR-018):
- **Normal** (default):
  ```
  Generating CRUD for Product...
  ✓ Model created: app/Models/Product.php
  ✓ Controller created: app/Http/Controllers/ProductController.php
  ...
  ✓ CRUD generation completed successfully (9 files created)
  ```

- **Quiet** (`--quiet`):
  ```
  [Only errors displayed, success is silent]
  ```

- **Verbose** (`--verbose` or `-v`):
  ```
  Validating resource name: Product
  Parsing field definitions: name:string, price:decimal
  Loading stub template: resources/stubs/model.stub
  Rendering model with tokens: RESOURCE_NAME=Product, TABLE_NAME=products
  Writing file: app/Models/Product.php (1,234 bytes)
  ...
  Atomic generation successful: 9 files committed
  ```

## Decision 10: Configuration Strategy

**Decision**: Publishable config with sensible defaults, minimal required configuration

**Rationale**:
- Laravel convention: publish config, customize as needed
- Zero-config installation for standard use cases
- Override paths, namespaces, stubs for custom project structures
- Supports monorepo and non-standard Laravel setups

**Alternatives Considered**:
- **No configuration**: Rejected - not flexible for non-standard projects
- **Required configuration**: Rejected - poor developer experience, violates "convention over configuration"
- **Environment crud-generators only**: Rejected - too granular, cluttered .env

**Implementation Notes**:

Config file structure (`config/crud-generator.php`):
```php
return [
    // Paths (relative to Laravel project root)
    'paths' => [
        'models' => 'app/Models',
        'controllers' => 'app/Http/Controllers',
        'requests' => 'app/Http/Requests',
        'resources' => 'app/Http/Resources',
        'vue_pages' => 'resources/js/pages',
    ],

    // Namespaces
    'namespaces' => [
        'models' => 'App\\Models',
        'controllers' => 'App\\Http\\Controllers',
        'requests' => 'App\\Http\\Requests',
        'resources' => 'App\\Http\\Resources',
    ],

    // Stub locations (relative to package or published location)
    'stubs' => resource_path('stubs/vendor/crud-generator'), // Fallback to package stubs

    // Default options (can be overridden by command flags)
    'defaults' => [
        'soft_deletes' => true,
        'auditing' => true,
        'generate_views' => true,
        'pagination' => 15,
    ],

    // Field type mappings (extensible)
    'field_types' => [
        'string' => ['migration' => 'string', 'validation' => 'string|max:255'],
        'text' => ['migration' => 'text', 'validation' => 'string'],
        'integer' => ['migration' => 'integer', 'validation' => 'integer'],
        'decimal' => ['migration' => 'decimal:10,2', 'validation' => 'numeric'],
        'boolean' => ['migration' => 'boolean', 'validation' => 'boolean'],
        'date' => ['migration' => 'date', 'validation' => 'date'],
        'datetime' => ['migration' => 'dateTime', 'validation' => 'date'],
        'timestamp' => ['migration' => 'timestamp', 'validation' => 'date'],
        'json' => ['migration' => 'json', 'validation' => 'json'],
    ],
];
```

Publish command:
```bash
php artisan vendor:publish --tag=crud-generator-config
php artisan vendor:publish --tag=crud-generator-stubs
```

## Summary

All technical decisions are finalized with clear rationale. No "NEEDS CLARIFICATION" items remain. The architecture leverages proven Laravel ecosystem tools (Spatie Package Tools, Laravel's Str helpers, Symfony Console, Orchestra Testbench) to minimize custom complexity while meeting all constitutional requirements and functional specifications. Implementation can proceed to Phase 1: Design & Contracts.
