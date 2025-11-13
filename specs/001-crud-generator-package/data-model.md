# Data Model: CRUD Generator Package

**Date**: 2025-01-13
**Feature**: 001-crud-generator-package

## Overview

This package operates on code generation artifacts, not traditional database entities. The "entities" below represent the domain model for code generation - the structured data that flows through the generation pipeline.

## Core Entities

### 1. CrudResource

Represents the entity being generated (e.g., Product, User, Company).

**Attributes**:
- `name: string` - Resource name in PascalCase (e.g., "Product")
- `namePlural: string` - Plural form in PascalCase (e.g., "Products")
- `tableName: string` - Database table name in snake_case plural (e.g., "products")
- `routeName: string` - Route name in kebab-case plural (e.g., "products")
- `namespace: string` - PHP namespace for generated classes (e.g., "App\\Models")
- `fields: FieldDefinition[]` - Collection of field definitions
- `relationships: RelationshipDefinition[]` - Collection of relationship definitions
- `options: GenerationOptions` - Generation configuration options

**Validation Rules**:
- `name`: required, alphanumeric, starts with letter, 1-50 characters
- `fields`: required, minimum 1 field
- Reserved words check: cannot be "File", "Directory", "Exception", etc.

**State Transitions**: Immutable once created (value object pattern)

**Example**:
```php
CrudResource {
    name: "Product",
    namePlural: "Products",
    tableName: "products",
    routeName: "products",
    namespace: "App\\Models",
    fields: [
        FieldDefinition { name: "name", type: "string", modifiers: [] },
        FieldDefinition { name: "price", type: "decimal", modifiers: [] }
    ],
    relationships: [],
    options: GenerationOptions { softDeletes: true, auditing: true, ... }
}
```

---

### 2. FieldDefinition

Represents a database column with name, type, and modifiers.

**Attributes**:
- `name: string` - Field name in snake_case (e.g., "product_name")
- `type: string` - Data type (string|text|integer|decimal|boolean|date|datetime|timestamp|json)
- `modifiers: string[]` - Array of modifiers (["nullable"], ["unique"], ["nullable", "unique"])
- `isNullable: bool` - Computed from modifiers (default: false)
- `isUnique: bool` - Computed from modifiers (default: false)

**Derived Properties**:
- `migrationColumnType: string` - Laravel migration column type (e.g., "string", "decimal:10,2")
- `validationRule: string` - Laravel validation rule (e.g., "required|string|max:255", "nullable|string|unique:products,name")
- `formInputType: string` - Vue form input component (e.g., "Input", "Textarea", "Checkbox")
- `phpType: string` - PHP type hint for model property (e.g., "string", "int", "bool", "Carbon")

**Validation Rules**:
- `name`: required, lowercase, alphanumeric + underscore, starts with letter
- `type`: required, must be in supported types list
- `modifiers`: optional, must be in allowed modifiers list (nullable, unique)

**State Transitions**: Immutable value object

**Example**:
```php
FieldDefinition {
    name: "email",
    type: "string",
    modifiers: ["unique", "nullable"],
    isNullable: true,
    isUnique: true,

    // Derived:
    migrationColumnType: "string",
    validationRule: "nullable|string|max:255|unique:users,email",
    formInputType: "Input",
    phpType: "?string"
}
```

---

### 3. RelationshipDefinition

Represents a model relationship with type, related model, and configuration.

**Attributes**:
- `type: string` - Relationship type (belongsTo|hasMany|hasOne|morphMany)
- `relatedModel: string` - Related model name in PascalCase (e.g., "User")
- `methodName: string` - Relationship method name (e.g., "user", "posts")
- `foreignKey: string?` - Custom foreign key (optional, auto-computed if null)
- `localKey: string?` - Custom local key (optional, auto-computed if null)

**Derived Properties**:
- `foreignKeyColumn: string` - Database foreign key column (e.g., "user_id")
- `eagerLoadPath: string` - Controller eager loading path (e.g., "user", "posts")
- `migrationConstraint: string` - Foreign key constraint syntax

**Validation Rules**:
- `type`: required, must be in supported types (belongsTo, hasMany, hasOne, morphMany)
- `relatedModel`: required, must exist or be generated
- `methodName`: required, camelCase, descriptive

**State Transitions**: Immutable value object

**Example**:
```php
RelationshipDefinition {
    type: "belongsTo",
    relatedModel: "User",
    methodName: "author",
    foreignKey: "author_id",
    localKey: null,

    // Derived:
    foreignKeyColumn: "author_id",
    eagerLoadPath: "author",
    migrationConstraint: "->foreignId('author_id')->constrained('users')->onDelete('cascade')"
}
```

---

### 4. GenerationOptions

Contains command options and configuration flags.

**Attributes**:
- `softDeletes: bool` - Include SoftDeletes trait (default: true)
- `auditing: bool` - Include Auditable contract (default: true)
- `generateViews: bool` - Generate Vue files (default: true)
- `tableName: string?` - Custom table name override (optional)
- `force: bool` - Overwrite existing files (default: false)
- `quiet: bool` - Suppress output (default: false)
- `verbose: bool` - Detailed logging (default: false)
- `withTests: bool` - Generate test stubs (default: false)

**Validation Rules**:
- Mutually exclusive: `quiet` and `verbose` cannot both be true
- Custom `tableName` must be valid SQL identifier (snake_case, alphanumeric + underscore)

**State Transitions**: Immutable value object

**Example**:
```php
GenerationOptions {
    softDeletes: true,
    auditing: true,
    generateViews: true,
    tableName: null,
    force: false,
    quiet: false,
    verbose: true,
    withTests: false
}
```

---

### 5. StubTemplate

Represents a template file for code generation.

**Attributes**:
- `name: string` - Stub file name (e.g., "model.stub", "controller.stub")
- `path: string` - Absolute path to stub file
- `content: string` - Raw template content with tokens
- `tokens: string[]` - List of required tokens (e.g., ["{{ RESOURCE_NAME }}", "{{ TABLE_NAME }}"])

**Methods**:
- `render(array $replacements): string` - Replace tokens with actual values
- `validate(): bool` - Check if all required tokens are present

**Validation Rules**:
- File must exist and be readable
- Content must be valid PHP/Vue syntax
- All `{{ TOKEN }}` placeholders must be in tokens list

**State Transitions**: Loaded → Validated → Rendered

**Example**:
```php
StubTemplate {
    name: "model.stub",
    path: "/path/to/stubs/model.stub",
    content: "<?php\n\nnamespace {{ NAMESPACE }};\n\nclass {{ RESOURCE_NAME }} extends Model\n{\n    use SoftDeletes;\n    ...",
    tokens: ["{{ NAMESPACE }}", "{{ RESOURCE_NAME }}", "{{ TABLE_NAME }}", "{{ FILLABLE_FIELDS }}"]
}
```

---

### 6. GeneratedFile

Represents a file created during CRUD generation.

**Attributes**:
- `type: string` - File type (model|controller|store-request|update-request|resource|index-vue|create-vue|edit-vue|form-vue)
- `path: string` - Absolute path where file will be created
- `content: string` - Rendered file content
- `exists: bool` - Whether file already exists at path
- `created: bool` - Whether file was successfully created

**Methods**:
- `write(): void` - Write content to filesystem
- `delete(): void` - Remove file from filesystem (for rollback)

**Validation Rules**:
- Path must be within Laravel project directories
- Parent directory must exist or be creatable
- If `exists: true` and `force: false`, generation should fail

**State Transitions**: Planned → Validated → Written → Committed (or Rolled Back)

**Example**:
```php
GeneratedFile {
    type: "model",
    path: "/path/to/app/Models/Product.php",
    content: "<?php\n\nnamespace App\\Models;\n\nuse Illuminate\\Database\\Eloquent\\Model;\n\nclass Product extends Model { ... }",
    exists: false,
    created: true
}
```

---

### 7. GenerationResult

Aggregates the outcome of a CRUD generation attempt.

**Attributes**:
- `resource: CrudResource` - The resource that was generated
- `files: GeneratedFile[]` - Collection of generated files
- `success: bool` - Whether generation completed successfully
- `error: string?` - Error message if `success: false`
- `duration: float` - Generation time in seconds
- `rollbackPerformed: bool` - Whether rollback was triggered

**Methods**:
- `wasSuccessful(): bool` - Check if generation succeeded
- `getCreatedFilePaths(): string[]` - Get list of created file paths
- `getSummary(): string` - Human-readable summary for console output

**State Transitions**: Pending → In Progress → Success/Failure (→ Rolled Back if Failure)

**Example**:
```php
GenerationResult {
    resource: CrudResource { name: "Product", ... },
    files: [
        GeneratedFile { type: "model", path: "app/Models/Product.php", created: true },
        GeneratedFile { type: "controller", path: "app/Http/Controllers/ProductController.php", created: true },
        ... // 9 files total
    ],
    success: true,
    error: null,
    duration: 2.34,
    rollbackPerformed: false
}
```

## Entity Relationships

```
CrudResource (1) ─── has many ───> (N) FieldDefinition
CrudResource (1) ─── has many ───> (N) RelationshipDefinition
CrudResource (1) ─── has one ─────> (1) GenerationOptions
CrudResource (1) ─── generates ──> (1) GenerationResult

GenerationResult (1) ─ contains ──> (N) GeneratedFile

StubTemplate (N) ─── renders to ──> (1) GeneratedFile
```

## Data Flow Pipeline

```
1. Command Input
   ↓
2. Parse → CrudResource
   ↓
3. Validate → CrudResource (validated)
   ↓
4. Load StubTemplates
   ↓
5. Render → GeneratedFile[] (planned)
   ↓
6. Write Files → GeneratedFile[] (created)
   ↓
7. Commit/Rollback → GenerationResult
```

## Validation Summary

| Entity | Validation Trigger | Key Rules |
|--------|-------------------|-----------|
| CrudResource | Construction | Name format, reserved word check, min 1 field |
| FieldDefinition | Construction | Name format, type whitelist, modifier whitelist |
| RelationshipDefinition | Construction | Type whitelist, related model existence (optional) |
| GenerationOptions | Construction | Mutual exclusivity (quiet/verbose), table name format |
| StubTemplate | Load | File existence, readability, token presence |
| GeneratedFile | Write | Path validity, parent directory existence, overwrite check |
| GenerationResult | Completion | File count (9 expected), success/rollback consistency |

## Performance Considerations

- **FieldDefinition parsing**: O(n) where n = field count, <1ms per field
- **StubTemplate rendering**: O(t) where t = token count, <100ms per template
- **File writing**: O(f) where f = file count, <5s for 9 files
- **Total generation**: Target <30 seconds (SC-001)

## Error Handling Strategy

- **Parse errors**: Immediate failure with clear message, no files created
- **Validation errors**: Immediate failure with field/option details, no files created
- **Template errors**: Immediate failure with stub name, no files created
- **File write errors**: Trigger rollback, delete all created files (FR-017)
- **Partial success**: Not allowed - atomic generation ensures all-or-nothing (FR-017)
