# Implementation Plan: CRUD Generator Package

**Branch**: `001-crud-generator-package` | **Date**: 2025-01-13 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `/specs/001-crud-generator-package/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

## Summary

A Laravel 12 package that generates complete CRUD modules from a single artisan command. The system produces all five architectural layers (Model, Controller, Form Requests, Resource, Vue Pages) following the established pattern in `example-files/`. Developers can scaffold a resource like `php artisan make:crud Product name:string price:decimal` and receive nine production-ready files with proper naming conventions, validation rules, and UI components. The package uses customizable stub templates, supports field modifiers (`:nullable`, `:unique`), provides generation options (--no-soft-deletes, --no-auditing, --no-views), and implements atomic generation with automatic rollback on failure. The technical approach leverages Spatie Laravel Package Tools for service provider bootstrapping, stub-based code generation with token replacement, Laravel's vendor:publish mechanism for customization, and comprehensive validation with PHPStan Level 5 + Pest architectural tests.

## Technical Context

**Language/Version**: PHP ^8.4 (compatible with 8.3)
**Primary Dependencies**:
  - Laravel 12.* (framework foundation)
  - Spatie Laravel Package Tools (service provider scaffolding)
  - Spatie Query Builder (generated controller patterns)
  - Owen-It Auditing (generated model trait)
  - Inertia.js (generated controller responses)

**Storage**: Files (stub templates, publishable assets) + Laravel's filesystem abstraction
**Testing**: Pest PHP with Orchestra Testbench (package testing environment)
**Target Platform**: Laravel 12 applications (web server, PHP 8.3+)
**Project Type**: Laravel Package (Composer installable, publishable assets)
**Performance Goals**:
  - CRUD generation: <30 seconds for complete module (all 9 files)
  - Command execution: <5 seconds for validation + file generation
  - Stub parsing: <100ms per template file

**Constraints**:
  - Must follow Laravel 12 conventions (Artisan commands, service providers, vendor:publish)
  - Generated code must pass PHPStan Level 5 without manual fixes
  - Generated code must pass Laravel Pint (PSR-12) without manual fixes
  - Generated Vue components must use TypeScript strict mode
  - Atomic generation: all files created or none (rollback on failure)
  - Stub templates must be publishable and customizable

**Scale/Scope**:
  - Support 10+ field types (string, text, integer, decimal, boolean, date, datetime, timestamp, json)
  - Generate 9 files per CRUD: 1 Model + 1 Controller + 2 Requests + 1 Resource + 4 Vue files
  - Support 3+ relationship types (belongsTo, hasMany, hasOne, morphMany)
  - Handle 10+ command options (--no-soft-deletes, --no-auditing, --no-views, --table, --force, --quiet, --verbose, etc.)
  - Customizable through 9+ stub templates
  - Target: reduce CRUD creation time by 80% (from ~60min to ~12min including customization)

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

### Principle I: Package-First Architecture ✅ PASS

**Requirement**: Every feature MUST be designed as a reusable Laravel package component with clear service provider integration, publishable assets, and independent testability using Orchestra Testbench.

**Compliance**:
- ✅ Package structure follows Spatie Laravel Package Tools conventions
- ✅ Service provider for command registration and asset publishing
- ✅ Publishable assets: stub templates (--tag=crud-generator-stubs), config (--tag=crud-generator-config)
- ✅ Orchestra Testbench for package testing environment
- ✅ Composer installable via `composer require vendor/crud-generator`

### Principle II: CRUD Pattern Consistency ✅ PASS

**Requirement**: All CRUD implementations MUST follow the five-layer pattern: Model (SoftDeletes + Auditable + Relationships), Controller (QueryBuilder + Inertia), Form Requests (Store/Update validation), Resources (API transformation), Vue Pages (Index/Create/Edit/Show + Form component).

**Compliance**:
- ✅ Package generates code following exact pattern from `example-files/`
- ✅ Stub templates enforce five-layer architecture
- ✅ Generated Models include SoftDeletes trait by default (unless --no-soft-deletes)
- ✅ Generated Models implement Auditable contract (unless --no-auditing)
- ✅ Generated Controllers use Spatie QueryBuilder patterns
- ✅ Generated Vue pages follow Index/Create/Edit + reusable Form component pattern

### Principle III: Test-First Development (NON-NEGOTIABLE) ✅ PASS

**Requirement**: Pest PHP tests MUST be written before implementation. Architecture tests MUST prevent debugging functions in production code. Tests cover: contract validation, integration (user journeys), unit (isolated logic).

**Compliance**:
- ✅ Package itself tested with Pest + Orchestra Testbench
- ✅ Architectural tests prevent `dd`, `dump`, `ray` in generated code
- ✅ Generated code includes test stubs (optional via --with-tests flag)
- ✅ Contract tests: command validation, file generation, rollback behavior
- ✅ Integration tests: complete CRUD generation scenarios from spec
- ✅ Unit tests: field parser, naming converter, stub template engine

### Principle IV: Type Safety & Static Analysis ✅ PASS

**Requirement**: TypeScript MUST be used for all frontend code with strict typing. Backend MUST maintain PHPStan Level 5 on `src/`, `config/`, `database/` with Octane compatibility checks. Laravel Pint enforces PSR-12.

**Compliance**:
- ✅ Package source code: PHPStan Level 5 enforced on `src/`, `config/`
- ✅ Generated backend code: passes PHPStan Level 5 without manual fixes (Success Criterion SC-002)
- ✅ Generated Vue components: TypeScript strict mode with proper interfaces
- ✅ Package source: Laravel Pint (PSR-12) enforced via composer format
- ✅ Generated code: passes Laravel Pint without manual fixes (Success Criterion SC-002)

### Principle V: Auditing & Soft Deletes ✅ PASS

**Requirement**: All models MUST implement SoftDeletes and Auditable contract. Hard deletes require justification.

**Compliance**:
- ✅ Generated Models include SoftDeletes trait by default
- ✅ Generated Models implement Auditable contract by default
- ✅ Command option --no-soft-deletes available with explicit developer choice
- ✅ Command option --no-auditing available with explicit developer choice
- ✅ Generated Controllers filter trashed records when SoftDeletes enabled

### Principle VI: N+1 Query Prevention ✅ PASS

**Requirement**: Controllers MUST eager load relationships using `with()` clauses. Spatie Query Builder provides consistent filtering/sorting with relationship loading.

**Compliance**:
- ✅ Generated Controllers use `QueryBuilder::for()` pattern
- ✅ Relationships (via --belongs-to, --has-many) automatically eager loaded
- ✅ Generated code includes `->with(['relationship'])` for defined relationships
- ✅ Controller index method uses Spatie Query Builder for filtering + eager loading

### Principle VII: Validation in Form Requests ✅ PASS

**Requirement**: Validation logic MUST reside in Form Request classes, never in controllers or models. Authorization logic MUST use Form Request `authorize()` method.

**Compliance**:
- ✅ Generated StoreRequest and UpdateRequest classes contain all validation rules
- ✅ Validation rules derived from field definitions (required, nullable, unique, type rules)
- ✅ Controllers remain thin orchestration layers
- ✅ Generated Form Requests include `authorize()` method (default: true, customizable)

### Quality Gates ✅ ALL PASS

- ✅ PHPStan Level 5 on package source code
- ✅ Laravel Pint (PSR-12) on package source code
- ✅ Pest Arch tests prevent debugging functions
- ✅ TypeScript compilation with zero errors
- ✅ Generated code passes PHPStan Level 5 (SC-002)
- ✅ Generated code passes Laravel Pint (SC-002)
- ✅ Test coverage for command validation, generation, rollback

**Initial Assessment**: ✅ ALL CONSTITUTION GATES PASS - No violations, proceed to Phase 0 research.

---

## Post-Design Constitution Re-Check

*Performed after Phase 1 design completion*

### Principle I: Package-First Architecture ✅ MAINTAINED

**Design Validation**:
- ✅ Service provider uses Spatie Laravel Package Tools `Package` fluent API
- ✅ Publishable stubs defined in `resources/stubs/` with tag `crud-generator-stubs`
- ✅ Publishable config defined in `config/crud-generator.php` with tag `crud-generator-config`
- ✅ Orchestra Testbench integration in test suite (tests/Feature/, tests/Unit/)
- ✅ Composer autoload PSR-4: `Vendor\\CrudGenerator\\` → `src/`

### Principle II: CRUD Pattern Consistency ✅ MAINTAINED

**Design Validation**:
- ✅ Generated code contracts enforce five-layer pattern (see contracts/generated-code.md)
- ✅ Stub templates replicate `example-files/` structure exactly
- ✅ Model stub includes SoftDeletes trait (conditional on --no-soft-deletes)
- ✅ Model stub implements Auditable contract (conditional on --no-auditing)
- ✅ Controller stub uses Spatie QueryBuilder with allowedFilters, allowedSorts
- ✅ Vue page stubs follow Index/Create/Edit + Form component pattern

### Principle III: Test-First Development ✅ MAINTAINED

**Design Validation**:
- ✅ Quickstart.md enforces Test → Implement → Validate workflow
- ✅ Test structure defined: tests/Arch/, tests/Feature/, tests/Unit/
- ✅ Architectural tests specified to prevent dd/dump/ray in generated code
- ✅ Each implementation phase starts with test creation
- ✅ 16 test files specified across Unit (4), Feature (8), Arch (1)

### Principle IV: Type Safety & Static Analysis ✅ MAINTAINED

**Design Validation**:
- ✅ Package code subject to PHPStan Level 5 (composer analyse)
- ✅ Generated backend code validated to pass PHPStan Level 5 (FR-002, SC-002)
- ✅ Generated Vue components use TypeScript strict mode with interfaces
- ✅ Resource interfaces defined (ProductResource, PaginatedResponse, Props)
- ✅ Laravel Pint enforced via composer format on package and generated code

### Principle V: Auditing & Soft Deletes ✅ MAINTAINED

**Design Validation**:
- ✅ Model stub includes SoftDeletes trait by default
- ✅ Model stub implements Auditable contract by default
- ✅ Command options --no-soft-deletes and --no-auditing available (explicit opt-out)
- ✅ Migration stub includes `$table->softDeletes()` when enabled

### Principle VI: N+1 Query Prevention ✅ MAINTAINED

**Design Validation**:
- ✅ Controller stub uses `QueryBuilder::for()` pattern
- ✅ Relationship support includes `->with(['relationship'])` eager loading
- ✅ Generated controllers follow Spatie Query Builder best practices
- ✅ Index method stub includes proper eager loading syntax

### Principle VII: Validation in Form Requests ✅ MAINTAINED

**Design Validation**:
- ✅ StoreRequest and UpdateRequest stubs contain all validation logic
- ✅ ValidationRuleBuilder maps field definitions to Laravel validation rules
- ✅ Controller stub uses Form Requests, no validation in controller methods
- ✅ Generated controllers remain thin orchestration layers

### Quality Gates ✅ ALL MAINTAINED

- ✅ PHPStan Level 5: Enforced via composer scripts, architectural tests
- ✅ Laravel Pint: Enforced via composer scripts, automatic in generation
- ✅ Pest Arch tests: Defined in tests/Arch/ArchTest.php to prevent debugging functions
- ✅ TypeScript strict: Vue stubs use proper interfaces and type annotations
- ✅ Generated code quality: Contracts specify PHPStan + Pint compliance (SC-002)

**Post-Design Assessment**: ✅ ALL CONSTITUTION GATES MAINTAINED - Design phase preserves all constitutional requirements. Implementation can proceed to Phase 2: Task Generation.

## Project Structure

### Documentation (this feature)

```text
specs/[###-feature]/
├── plan.md              # This file (/speckit.plan command output)
├── research.md          # Phase 0 output (/speckit.plan command)
├── data-model.md        # Phase 1 output (/speckit.plan command)
├── quickstart.md        # Phase 1 output (/speckit.plan command)
├── contracts/           # Phase 1 output (/speckit.plan command)
└── tasks.md             # Phase 2 output (/speckit.tasks command - NOT created by /speckit.plan)
```

### Source Code (repository root)

```text
# Laravel Package Structure (Spatie Laravel Package Tools pattern)
src/
├── Commands/
│   └── MakeCrudCommand.php          # Main artisan command
├── Generators/
│   ├── ModelGenerator.php           # Model file generator
│   ├── ControllerGenerator.php      # Controller file generator
│   ├── RequestGenerator.php         # Form Request generator (Store/Update)
│   ├── ResourceGenerator.php        # API Resource generator
│   └── VueGenerator.php             # Vue page/component generator
├── Parsers/
│   ├── FieldDefinitionParser.php    # Parse "name:string:nullable:unique"
│   ├── RelationshipParser.php       # Parse --belongs-to, --has-many options
│   └── ValidationRuleBuilder.php    # Build validation rules from field defs
├── Support/
│   ├── NamingConverter.php          # PascalCase/snake_case/kebab-case
│   ├── StubRenderer.php             # Token replacement in stubs
│   └── FileRollback.php             # Atomic generation rollback manager
└── CrudGeneratorServiceProvider.php # Package service provider

config/
└── crud-generator.php               # Publishable package configuration

resources/stubs/                     # Publishable stub templates
├── model.stub                       # Model template
├── controller.stub                  # Controller template
├── store-request.stub               # StoreRequest template
├── update-request.stub              # UpdateRequest template
├── resource.stub                    # API Resource template
├── index.vue.stub                   # Vue Index page template
├── create.vue.stub                  # Vue Create page template
├── edit.vue.stub                    # Vue Edit page template
└── form.vue.stub                    # Vue Form component template

tests/
├── Arch/
│   └── ArchTest.php                 # Prevent dd/dump/ray in generated code
├── Feature/
│   ├── CrudGenerationTest.php       # Complete generation scenarios
│   ├── AtomicRollbackTest.php       # Rollback on failure tests
│   ├── FieldModifiersTest.php       # :nullable, :unique tests
│   └── CommandOptionsTest.php       # --no-soft-deletes, --no-auditing tests
└── Unit/
    ├── FieldDefinitionParserTest.php # Field parsing logic
    ├── NamingConverterTest.php       # Name conversion logic
    ├── ValidationRuleBuilderTest.php # Validation rule generation
    └── StubRendererTest.php          # Token replacement logic

example-files/                       # Reference CRUD implementation
└── [Existing structure - used as source pattern]
```

**Structure Decision**: Laravel Package structure following Spatie Laravel Package Tools conventions. The package is self-contained with `src/` for implementation, `config/` for publishable configuration, `resources/stubs/` for publishable templates, and `tests/` using Pest + Orchestra Testbench. The `example-files/` directory serves as the reference implementation that stub templates replicate. This structure enables Composer installation, vendor:publish customization, and independent package testing.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

**No violations** - All constitution gates pass. The package architecture follows established Laravel package patterns without introducing unnecessary complexity.
