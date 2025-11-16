<!--
Sync Impact Report:
- Version: 1.0.0 → 1.0.1 (PATCH)
- Constitution Status: Version clarification update
- Rationale: Clarified stack versions - Laravel 12 (not 11+), Shadcn-vue, Tailwind CSS 4
- Modified Principles: None (principles unchanged)
- Modified Sections: Technical Standards > Stack Requirements (clarified versions)
- Added Sections: None
- Removed Sections: None
- Templates Status:
  ✅ plan-template.md - No changes needed (Constitution Check section still aligns)
  ✅ spec-template.md - No changes needed (User scenarios still align)
  ✅ tasks-template.md - No changes needed (Task organization still aligns)
- Follow-up TODOs: None
- Previous Version History:
  - 1.0.0 (2025-01-13): Initial constitution for Laravel Package Skeleton with CRUD focus
-->

# Laravel CRUD Package Constitution

## Core Principles

### I. Package-First Architecture

Every feature MUST be designed as a reusable Laravel package component. Packages MUST be self-contained with clear service provider integration, publishable assets (config, views, migrations), and independent testability using Orchestra Testbench. No organizational-only packages without clear functional purpose.

**Rationale**: Ensures modularity, reusability across projects, and maintains package ecosystem standards. Spatie's Laravel Package Tools establishes proven patterns we follow.

### II. CRUD Pattern Consistency

All CRUD implementations MUST follow the established five-layer pattern: Model (SoftDeletes + Auditable + Relationships), Controller (QueryBuilder + Inertia), Form Requests (Store/Update validation), Resources (API transformation), and Vue Pages (Index/Create/Edit/Show + reusable Form component). Deviations require documented justification.

**Rationale**: Consistency reduces cognitive load, enables faster onboarding, and ensures predictable behavior. Pattern proven in example-files/ demonstrates full-stack integration.

### III. Test-First Development (NON-NEGOTIABLE)

Pest PHP tests MUST be written before implementation. Architecture tests MUST prevent debugging functions (`dd`, `dump`, `ray`) in production code. Tests cover: contract validation (API endpoints), integration (user journeys), and unit (isolated logic). Orchestra Testbench provides Laravel package testing environment.

**Rationale**: TDD catches issues early when cheaper to fix. Architectural tests enforce code quality gates automatically. No production code ships without test coverage.

### IV. Type Safety & Static Analysis

TypeScript MUST be used for all frontend code (Vue 3 Composition API) with strict typing for props, interfaces, and composables. Backend MUST maintain PHPStan Level 5 analysis on `src/`, `config/`, `database/` with Octane compatibility checks and model property validation. Laravel Pint enforces PSR-12 code style.

**Rationale**: Type safety prevents runtime errors, enables better IDE support, and documents intent. Static analysis catches bugs before deployment.

### V. Auditing & Soft Deletes

All models MUST implement `SoftDeletes` for data retention and audit trail preservation. Models requiring change tracking MUST implement `Auditable` contract (Owen-It Auditing). Hard deletes require explicit justification and security review.

**Rationale**: Data recovery capability is critical for production systems. Audit trails support compliance, debugging, and user trust. Irreversible actions need higher scrutiny.

### VI. N+1 Query Prevention

Controllers MUST eager load relationships using `with()` clauses. Query performance MUST be verified during code review. Spatie Query Builder's `QueryBuilder::for()` provides consistent filtering/sorting with relationship loading.

**Rationale**: N+1 queries cause performance degradation at scale. Eager loading is cheaper than reactive optimization. Query Builder patterns enforce best practices.

### VII. Validation in Form Requests

Validation logic MUST reside in Form Request classes (`StoreRequest`, `UpdateRequest`), never in controllers or models. Authorization logic MUST use Form Request `authorize()` method. Controllers remain thin orchestration layers.

**Rationale**: Separation of concerns keeps controllers readable. Form Requests are reusable, testable, and follow Laravel conventions.

## Technical Standards

### Stack Requirements

**Backend**:
- PHP ^8.4 (8.3 compatible)
- Laravel 12.*
- Spatie Laravel Package Tools for service providers
- Spatie Query Builder for advanced filtering/sorting
- Owen-It Auditing for model change tracking
- Pest PHP for testing framework

**Frontend**:
- Vue 3 with Composition API
- TypeScript (strict mode)
- Inertia.js for server-driven SPA
- Shadcn-vue for headless UI components
- Tailwind CSS 4 for utility-first styling
- TanStack Table for data tables
- VueUse for composable utilities
- Lucide Icons

### Code Quality Gates

- PHPStan Level 5 (minimum) - analysis MUST pass
- Laravel Pint (PSR-12) - code style MUST be enforced
- Pest Arch tests - no debugging functions in production
- TypeScript compilation - zero errors allowed
- Octane compatibility - verified via PHPStan

### File Organization Standards

**Package Structure**:
```
src/                    # Package source code
├── Commands/          # Artisan commands
├── Facades/           # Package facades
└── [Feature]ServiceProvider.php

config/                # Publishable configuration
resources/views/       # Publishable views
database/migrations/   # Publishable migrations
tests/                 # Pest PHP tests
example-files/         # CRUD implementation examples
```

**CRUD Implementation Structure**:
```
app/Models/                              # Eloquent models
app/Http/Controllers/                    # RESTful controllers
app/Http/Requests/[Resource]/           # Form validation
app/Http/Resources/                      # API transformation
resources/js/pages/[resource]/          # Vue pages
resources/js/pages/[resource]/components/  # Reusable forms
```

### Query Builder Pattern

Standard filtering MUST follow this implementation:
```php
QueryBuilder::for(Model::class)
    ->allowedFilters([
        AllowedFilter::callback('search', fn($q, $v) => /* multi-field search */),
        AllowedFilter::partial('field_name'),
        AllowedFilter::exact('status_field'),
    ])
    ->allowedSorts(['field1', 'field2', 'created_at'])
    ->defaultSort('-created_at')
    ->with(['relationship'])  // Prevent N+1
    ->paginate(15)
    ->withQueryString();
```

### File Upload Pattern

File uploads MUST follow this pattern:
1. Check presence with `hasFile()`
2. Store in organized paths: `[resource]/[type]` (e.g., `companies/logos`)
3. Use `public` disk for web-accessible files
4. Delete old files on update: `\Storage::disk('public')->delete($old)`

## Development Workflow

### Package Initialization

Before using this skeleton, run configuration script to replace placeholders:
```bash
php ./configure.php
```

This replaces `:vendor_slug`, `:package_slug`, `:author_name`, etc. throughout codebase.

### Testing Commands

```bash
composer test              # Run all Pest tests
composer test-coverage     # Tests with coverage report
vendor/bin/pest --filter=arch  # Architecture tests only
vendor/bin/pest tests/ExampleTest.php  # Single test file
```

### Code Quality Commands

```bash
composer analyse  # PHPStan static analysis
composer format   # Laravel Pint code style fixes
```

### Git Workflow

- Feature branches MUST be created from `main`
- Commits MUST be incremental with descriptive messages
- PRs MUST pass all CI checks (tests, PHPStan, Pint)
- GitHub Actions run tests on PHP 8.4/8.3 × Laravel 12/11 × Ubuntu/Windows

### Code Review Requirements

Reviewers MUST verify:
- [ ] Tests written before implementation (TDD)
- [ ] PHPStan Level 5 passes
- [ ] Laravel Pint style enforced
- [ ] No debugging functions (`dd`, `dump`, `ray`)
- [ ] Relationships eager loaded (N+1 prevention)
- [ ] Validation in Form Requests, not controllers
- [ ] SoftDeletes implemented on models
- [ ] TypeScript strict typing on frontend
- [ ] CRUD pattern followed (if applicable)

## Governance

This Constitution supersedes all other development practices. Amendments require:
1. Documented rationale for change
2. Impact analysis on existing codebase
3. Template consistency updates (plan-template.md, spec-template.md, tasks-template.md)
4. Team approval before ratification

**Versioning Policy**:
- MAJOR: Backward incompatible principle removals or redefinitions
- MINOR: New principle/section added or materially expanded guidance
- PATCH: Clarifications, wording, typo fixes, non-semantic refinements

**Compliance Review**:
- All PRs MUST verify Constitution compliance
- CI MUST enforce automated quality gates (PHPStan, Pint, Pest Arch)
- Quarterly reviews assess principle effectiveness
- Complexity additions MUST be justified with simpler alternatives documented

**Runtime Guidance**: See `CLAUDE.md` for AI agent development guidance and `README.md` for package documentation standards.

**Version**: 1.0.1 | **Ratified**: 2025-01-13 | **Last Amended**: 2025-01-13
