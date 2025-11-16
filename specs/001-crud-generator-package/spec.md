# Feature Specification: CRUD Generator Package

**Feature Branch**: `001-crud-generator-package`
**Created**: 2025-01-13
**Status**: Draft
**Input**: User description: "quiero configurar en implementar un paquete para la generacion de cruds usando la estructtura de ejemplo '/Volumes/externo/proyectos/isaac/crud-inertia-shadcn/example-files' Quiero que la creacion funcione con comandos y stubs"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Generate Basic CRUD with Single Command (Priority: P1)

A developer wants to quickly scaffold a complete CRUD module for a new resource (e.g., "Product", "Employee") by running a single command. The system should generate all five layers following the established pattern: Model, Controller, Form Requests, Resource, and Vue pages with proper naming conventions and relationships.

**Why this priority**: This is the core value proposition - automating repetitive CRUD creation. Without this, the package has no functionality. This delivers immediate productivity gains.

**Independent Test**: Can be fully tested by installing package via Composer, optionally publishing stubs (`php artisan vendor:publish --tag=crud-generator-stubs`), running the command with a resource name (e.g., `php artisan make:crud Product name:string price:decimal`), and verifying all 10 files are created with correct content and naming.

**Acceptance Scenarios**:

1. **Given** a Laravel project with the package installed, **When** developer runs `make:crud Product name:string price:decimal`, **Then** system generates Model, Controller, StoreRequest, UpdateRequest, Resource, and four Vue files (Index, Create, Edit, Form component) with Product entity properly configured
2. **Given** a resource name with multiple words (e.g., "UserProfile"), **When** command is executed, **Then** all files use correct naming conventions (UserProfile model, UserProfileController, user-profiles routes, user_profiles table)
3. **Given** field definitions with different data types (string, integer, boolean, date), **When** CRUD is generated, **Then** migrations include correct column types (NOT NULL by default), Form Requests have appropriate validation rules (required by default), and Vue forms show correct input components
4. **Given** a field with `:nullable` suffix (e.g., `description:text:nullable`), **When** CRUD is generated, **Then** migration creates nullable column, validation rule includes nullable, and Vue form marks field as optional
5. **Given** a field with `:unique` suffix (e.g., `email:string:unique`), **When** CRUD is generated, **Then** migration creates unique index, validation rule includes unique constraint check, and error handling for duplicate values is present
6. **Given** a field with combined modifiers (e.g., `slug:string:unique:nullable`), **When** CRUD is generated, **Then** migration creates nullable column with unique index, validation rule includes both nullable and unique constraints
7. **Given** a previously generated CRUD resource, **When** developer runs the command again for the same resource, **Then** system prompts for confirmation before overwriting or skips generation with warning message

---

### User Story 2 - Customize Generated Code with Options (Priority: P2)

A developer wants to customize the generated CRUD to match project-specific requirements by passing options to the command, such as disabling soft deletes, excluding auditing, customizing table names, or generating only specific layers (e.g., only backend without Vue files).

**Why this priority**: Real projects have varying requirements. Some resources don't need soft deletes or auditing. This flexibility makes the package useful across different project types without requiring manual edits.

**Independent Test**: Can be tested independently by running commands with different flag combinations (e.g., `--no-soft-deletes`, `--no-auditing`, `--table=custom_products`) and verifying generated code reflects those options.

**Acceptance Scenarios**:

1. **Given** a resource that shouldn't use soft deletes, **When** developer runs `make:crud Product --no-soft-deletes`, **Then** Model doesn't include SoftDeletes trait and Controller doesn't filter trashed records
2. **Given** a need for custom table name, **When** developer runs `make:crud Product --table=inventory_products`, **Then** Model specifies `protected $table = 'inventory_products'` and migration creates the custom table name
3. **Given** a backend-only API resource, **When** developer runs `make:crud Product --no-views`, **Then** system generates Model, Controller, Requests, and Resource but skips all Vue files
4. **Given** a simple resource without change tracking, **When** developer runs `make:crud Product --no-auditing`, **Then** Model doesn't implement Auditable contract and related imports are excluded

---

### User Story 3 - Define Relationships in Generated Code (Priority: P3)

A developer wants to define relationships (belongsTo, hasMany, morphMany) when generating CRUD so that Models include relationship methods and Controllers eager load them automatically. For example, generating a "Post" CRUD that belongs to "User" should include the relationship method and eager loading in queries.

**Why this priority**: Relationships are fundamental to relational data models. Automating this reduces boilerplate and ensures N+1 prevention patterns are followed from the start. This is P3 because basic CRUD can function without relationships initially.

**Independent Test**: Can be tested independently by running command with relationship flags (e.g., `make:crud Post --belongs-to=User --has-many=Comment`) and verifying Model methods and Controller eager loading are generated.

**Acceptance Scenarios**:

1. **Given** a resource with belongsTo relationship, **When** developer runs `make:crud Post --belongs-to=User`, **Then** Post Model includes `user()` relationship method, migration adds `user_id` foreign key, and Controller eager loads user with `->with(['user'])`
2. **Given** a resource with multiple relationships, **When** developer runs `make:crud Company --has-many=User,Headquarters`, **Then** Model includes both `users()` and `headquarters()` methods and Controller lists them in eager loading
3. **Given** a polymorphic relationship requirement, **When** developer runs `make:crud Address --morph-many=addressable`, **Then** Model includes `morphMany` method and migration creates addressable_type and addressable_id columns
4. **Given** relationships with custom naming, **When** developer runs `make:crud Order --belongs-to=User:customer`, **Then** Model creates `customer()` method returning belongsTo User with proper foreign key configuration

---

### Edge Cases

- What happens when a resource name conflicts with an existing PHP class (e.g., "File", "Directory", "Exception")? System should detect reserved words and warn developer or prefix with project namespace.
- How does system handle invalid field type specifications (e.g., `name:invalidtype`)? System should validate field types against supported database column types and reject invalid types with clear error message.
- What happens when generating CRUD with relationships to non-existent models? System should validate that referenced models exist or provide option to generate them as well.
- How does system handle very long resource names (e.g., "VeryLongResourceNameThatExceedsReasonableLimits")? System should enforce reasonable name length limits (e.g., 50 characters) for maintainability.
- What happens when developer runs command in a project without required dependencies (Laravel 12, Inertia.js, Shadcn-vue, Tailwind 4, Spatie packages)? System should check for required dependencies and provide clear installation instructions if missing, including minimum version requirements.
- What happens when command fails midway through generation (e.g., file write error, template parsing failure)? System MUST implement automatic rollback - all created files are deleted to prevent partial/broken state. Generation is atomic: all files succeed or none are created.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST generate all five CRUD layers (10 files total: Model, Controller, 2 Form Requests, Resource, 4 Vue files, Migration) from a single command with resource name and field definitions
- **FR-002**: System MUST support field type definitions including string, text, integer, decimal, boolean, date, datetime, timestamp, and json types with automatic migration column type mapping. Fields are required (NOT NULL) by default; append `:nullable` suffix for optional fields (e.g., `description:text:nullable`). Append `:unique` suffix for unique constraints (e.g., `email:string:unique`). Modifiers can be combined (e.g., `slug:string:unique:nullable`)
- **FR-003**: System MUST apply consistent naming conventions across all generated files (PascalCase for classes, snake_case for tables, kebab-case for routes, plural for collections)
- **FR-004**: Generated Models MUST include SoftDeletes trait by default (unless disabled) and Auditable contract implementation with proper imports
- **FR-005**: Generated Controllers MUST use Spatie QueryBuilder with allowedFilters for search and field filtering, allowedSorts for column sorting, and default sort by created_at descending
- **FR-006**: System MUST generate separate StoreRequest and UpdateRequest classes with validation rules derived from field definitions (e.g., string fields get required|string|max:255, nullable fields get nullable|string|max:255, unique fields get required|string|unique:table_name,column, integer fields get required|integer)
- **FR-007**: Generated Vue Index pages MUST include Shadcn-vue DataTable component with search, filtering, sorting, pagination, and AlertDialog for delete confirmation following example pattern
- **FR-008**: Generated Vue Create/Edit pages MUST use a reusable Form component with proper Inertia form handling and validation error display
- **FR-009**: System MUST generate migrations with proper foreign key constraints for relationships, unique indexes for fields with `:unique` modifier, and indexes for commonly searched fields
- **FR-010**: System MUST provide command options to customize generation (--no-soft-deletes, --no-auditing, --no-views, --table=custom_name, --force to overwrite)
- **FR-018**: Command MUST provide normal output with progress indicators by default, support `--quiet` flag to suppress all output except errors, and `--verbose` flag for detailed step-by-step logging of file creation and template processing
- **FR-011**: Generated code MUST follow project Constitution requirements (Test-First, Type Safety, N+1 Prevention, Validation in Form Requests)
- **FR-012**: System MUST use stub templates that can be published and customized by developers for project-specific modifications via `php artisan vendor:publish --tag=crud-generator-stubs`
- **FR-019**: Package MUST be installable via standard Composer (`composer require vendor/crud-generator`) and provide publishable assets (stubs, config) using Laravel's vendor:publish command with appropriate tags
- **FR-013**: Command MUST validate resource name format (starts with letter, alphanumeric only) and field definitions (valid type, valid name) before generation
- **FR-017**: System MUST implement atomic generation with automatic rollback - if any file creation fails, all previously created files MUST be deleted to prevent partial/broken state
- **FR-014**: System MUST support relationship definitions through command options (--belongs-to, --has-many, --has-one, --morph-many) with automatic eager loading in Controller
- **FR-015**: Generated code MUST include proper TypeScript interfaces in Vue files for type safety (Resource interface, PaginatedResponse, props typing)
- **FR-016**: Generated Vue components MUST use Shadcn-vue UI components (Button, Input, Badge, Select, AlertDialog, Popover) with Tailwind CSS 4 utility classes for styling

### Key Entities

- **CRUD Resource**: Represents the entity being generated (e.g., Product, User, Company). Contains name, fields with types, optional relationships, and generation options.
- **Field Definition**: Represents a database column with name, data type, and modifiers (`:nullable`, `:unique`). Fields are required by default. Modifiers can be combined (e.g., `email:string:unique:nullable`). Maps to migration column (NOT NULL, nullable, unique index), validation rule (required, nullable, unique), and form input (required indicator, uniqueness error handling).
- **Stub Template**: Represents a template file for each layer (model.stub, controller.stub, request.stub, etc.) with placeholder tokens that get replaced during generation.
- **Relationship Definition**: Represents a model relationship with type (belongsTo, hasMany, etc.), related model name, optional custom method name, and foreign key configuration.
- **Generation Configuration**: Contains command options like soft delete flag, auditing flag, table name override, view generation flag, and force overwrite flag.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Developers can generate a complete CRUD module (all 10 files) in under 30 seconds with a single command execution
- **SC-002**: Generated code passes all Constitution quality gates (PHPStan Level 5, Laravel Pint, Pest Arch tests) without requiring manual fixes
- **SC-003**: 90% of common CRUD use cases can be handled without editing generated code (only customization being business logic additions)
- **SC-004**: Time to create a new CRUD module is reduced by 80% compared to manual creation (from ~60 minutes to ~12 minutes including customization)
- **SC-005**: Generated code follows 100% of the established pattern in example-files (verified by structural comparison)
- **SC-006**: Command provides clear, actionable error messages for 100% of invalid inputs (bad resource names, invalid field types, missing dependencies)
- **SC-008**: Command provides progress feedback during generation (normal mode) with support for silent operation (--quiet for CI/CD) and detailed debugging (--verbose for troubleshooting)
- **SC-007**: Developers can customize 100% of stub templates through publishing mechanism for project-specific modifications

## Clarifications

### Session 2025-01-13

- Q: When the CRUD generation command fails midway (e.g., after creating Model and Controller but before creating Vue files), what should happen to already-created files? → A: Automatic rollback - delete all created files on any failure
- Q: How should nullable/optional fields be specified in the command syntax? → A: Fields are required by default, use `:nullable` suffix for optional (e.g., `description:text:nullable`)
- Q: What level of output/logging should the command provide during CRUD generation? → A: Normal output with progress indicators, `--quiet` flag to suppress, `--verbose` flag for detailed logging
- Q: How should unique constraints be specified for fields that require uniqueness (e.g., email, username)? → A: Use `:unique` suffix for unique constraint (e.g., `email:string:unique`)
- Q: How should the package be installed and configured in a Laravel project? → A: Standard Composer package with `php artisan vendor:publish` for stubs and config

### Assumptions

- Developers have Laravel 12 project with Composer and Artisan available
- Package will be installed via Composer using standard Laravel package installation workflow
- Required dependencies are installed or can be installed:
  - Inertia.js for server-driven SPA
  - Spatie Query Builder for advanced filtering/sorting
  - Owen-It Auditing for model change tracking
  - Pest PHP for testing framework
- Vue 3 with TypeScript is configured in the project
- Shadcn-vue components are installed and configured
- Tailwind CSS 4 is installed and configured
- Database migrations can be run to create generated tables
- Developers understand basic Laravel artisan command syntax and CRUD concepts
- Project follows standard Laravel directory structure (app/Models, app/Http/Controllers, resources/js/pages)
- Node.js and npm/pnpm/yarn are available for frontend asset compilation
