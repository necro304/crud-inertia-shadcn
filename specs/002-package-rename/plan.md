# Implementation Plan: Package Rename to necro304/crud-inertia-shadcn

**Branch**: `002-package-rename` | **Date**: 2025-11-15 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/002-package-rename/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/commands/plan.md` for the execution workflow.

## Summary

Rename the Laravel package from its current placeholder configuration to `necro304/crud-inertia-shadcn`. This involves updating package metadata (composer.json), documentation files (README.md, CLAUDE.md), configuration script (configure.php), and ensuring all namespace references and autoload paths remain functional. The rename preserves existing functionality while establishing the proper package identity for distribution via Packagist.

## Technical Context

**Language/Version**: PHP ^8.4 (8.3 compatible)
**Primary Dependencies**: Laravel 12.*, Spatie Laravel Package Tools, Composer for package management
**Storage**: File-based (composer.json, configuration files, documentation)
**Testing**: Pest PHP with Orchestra Testbench for package testing
**Target Platform**: Laravel package ecosystem (cross-platform: Linux, macOS, Windows)
**Project Type**: Laravel package (single project structure with publishable assets)
**Performance Goals**: Instant package name resolution (<1s composer operations), zero runtime performance impact (metadata-only changes)
**Constraints**: Must preserve PSR-4 autoloading compliance, maintain backward compatibility for existing CRUD generation functionality, no breaking changes to public API
**Scale/Scope**: ~50 files affected (composer.json, README, CLAUDE.md files, configure.php, namespace references in src/)

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

### Principle I: Package-First Architecture
**Status**: ✅ PASS
**Assessment**: This feature maintains package-first architecture by updating package metadata without changing the modular structure. All service provider integration, publishable assets, and Orchestra Testbench testing remain unchanged.

### Principle II: CRUD Pattern Consistency
**Status**: ✅ PASS (N/A - Metadata Change Only)
**Assessment**: This is a package metadata rename, not a CRUD implementation. No changes to the five-layer CRUD pattern. Existing CRUD examples in example-files/ remain unaffected.

### Principle III: Test-First Development
**Status**: ✅ PASS
**Assessment**: Existing Pest PHP tests will be run to verify package functionality after rename. Tests verify: (1) composer.json validity, (2) PSR-4 autoloading works, (3) service provider registration succeeds, (4) artisan commands appear correctly.

### Principle IV: Type Safety & Static Analysis
**Status**: ✅ PASS
**Assessment**: PHPStan Level 5 analysis and Laravel Pint style checks will run post-rename to ensure no regressions. No PHP or TypeScript code logic changes, only metadata and documentation updates.

### Principle V: Auditing & Soft Deletes
**Status**: ✅ PASS (N/A - No Database Changes)
**Assessment**: This feature doesn't involve models or database changes. SoftDeletes and Auditable implementations remain unchanged.

### Principle VI: N+1 Query Prevention
**Status**: ✅ PASS (N/A - No Query Changes)
**Assessment**: No controller or query logic changes. All existing QueryBuilder patterns with eager loading remain unchanged.

### Principle VII: Validation in Form Requests
**Status**: ✅ PASS (N/A - No Validation Changes)
**Assessment**: No validation logic changes. Form Request patterns remain unchanged.

### Code Quality Gates
**Enforcement**: All quality gates will run post-rename:
- ✅ PHPStan Level 5 analysis
- ✅ Laravel Pint (PSR-12) style check
- ✅ Pest architecture tests (no debug functions)
- ✅ Composer validate (package metadata integrity)
- ✅ PSR-4 autoload verification

**Result**: ALL GATES PASS - No constitution violations. This is a metadata-only change preserving all architectural principles.

---

## Post-Design Constitution Re-Check

*Re-evaluated after Phase 1 (Design & Contracts) completion*

**Status**: ✅ ALL GATES STILL PASS

**Design Artifacts Created**:
1. research.md - Technical decisions documented
2. data-model.md - Configuration entity definitions
3. contracts/README.md - API stability confirmed (no breaking changes)
4. quickstart.md - Implementation guide

**Constitution Compliance Verification**:

### Principle I: Package-First Architecture
**Post-Design Status**: ✅ CONFIRMED PASS
- Design maintains service provider pattern
- Publishable assets structure unchanged
- Orchestra Testbench testing approach preserved
- No organizational-only changes (functional package identity update)

### Principle II: CRUD Pattern Consistency
**Post-Design Status**: ✅ CONFIRMED PASS (N/A)
- No CRUD layer changes in design
- Five-layer pattern remains intact
- Example files unaffected

### Principle III: Test-First Development
**Post-Design Status**: ✅ CONFIRMED PASS
- Quickstart includes test verification steps
- Existing Pest test suite validates rename
- No new features requiring TDD (metadata only)

### Principle IV: Type Safety & Static Analysis
**Post-Design Status**: ✅ CONFIRMED PASS
- PHPStan Level 5 enforced in quickstart
- Namespace changes preserve type safety
- No TypeScript changes (backend-only rename)

### Principle V-VII: Data Concerns
**Post-Design Status**: ✅ CONFIRMED PASS (N/A)
- No models, queries, or validation logic affected
- data-model.md confirms zero database impact

**Final Assessment**: Design phase introduces ZERO new complexity. All constitution principles remain satisfied.

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
# Laravel Package Structure (Affected by Rename)
composer.json                    # Package name, author, namespace, repository URLs
README.md                        # Installation instructions, package description
CLAUDE.md                       # AI agent development guidance
example/CLAUDE.md               # Example project guidance
configure.php                   # Interactive configuration script

src/                            # Package source (namespace references)
├── Commands/                   # Artisan command classes
├── Facades/                    # Package facades
├── Support/                    # Support classes
└── CrudGeneratorServiceProvider.php  # Main service provider

config/                         # Publishable configuration
resources/views/                # Publishable views (if any)
database/migrations/            # Publishable migrations (if any)

tests/                          # Pest PHP test suite
├── Pest.php                    # Test configuration
├── TestCase.php                # Orchestra Testbench base
├── ArchTest.php                # Architecture validation
└── *.php                       # Feature/unit tests

example-files/                  # CRUD implementation examples
└── [Laravel app structure]     # Example usage documentation
```

**Structure Decision**: Laravel package structure (Option 1: Single project). This is a metadata rename affecting primarily:
1. Root-level files: composer.json, README.md, CLAUDE.md, configure.php
2. Namespace references in src/ directory PHP files
3. Documentation files in example/ directory
4. No changes to actual package functionality or directory structure

## Complexity Tracking

**Status**: No complexity violations detected.

This feature is a straightforward metadata rename with zero architectural complexity additions. All constitution principles pass without exceptions.

---

## Planning Summary

### Phase 0: Research - ✅ COMPLETE

**Deliverable**: `research.md`

**Key Research Outcomes**:
1. Composer package naming conventions verified (`vendor/package` format)
2. PSR-4 autoloading requirements documented
3. Laravel package service provider patterns confirmed
4. Packagist registration process researched
5. Testing strategy defined (composer validate + Pest suite)
6. Documentation update requirements identified
7. Namespace migration path analyzed
8. configure.php compatibility verified
9. All technical unknowns resolved

**Decision**: Use `necro304/crud-inertia-shadcn` with PSR-4 namespace `Necro304\CrudInertiaShadcn\`

### Phase 1: Design & Contracts - ✅ COMPLETE

**Deliverables**:
- `data-model.md` - Configuration entity definitions
- `contracts/README.md` - API stability confirmation
- `quickstart.md` - Step-by-step implementation guide
- CLAUDE.md updated with new technology context

**Design Outcomes**:
1. Three configuration entities defined (Package Metadata, Documentation, Script State)
2. Validation rules specified (composer validate, PSR-4 compliance, PHPStan)
3. Zero API breaking changes confirmed
4. State transition machine documented (SKELETON → CONFIGURED → PUBLISHED → VERSIONED)
5. ~50 files identified for updates
6. 30-45 minute implementation timeline estimated

**Constitution Re-Check**: ALL GATES PASS (no violations introduced by design)

### Next Phase: Tasks Generation

Use `/speckit.tasks` command to generate `tasks.md` with actionable implementation checklist.

**Expected Task Categories**:
1. Configuration updates (composer.json)
2. Namespace refactoring (src/, tests/)
3. Documentation updates (README, CLAUDE.md)
4. Script updates (configure.php)
5. Validation & testing
6. Git commit & push

---

## Risk Assessment

**Overall Risk**: 🟢 LOW

**Risk Factors**:
| Risk | Likelihood | Impact | Mitigation |
|------|-----------|--------|------------|
| Typo in package name | Low | High | composer validate --strict |
| Broken autoloading | Low | High | composer dump-autoload + PHPStan |
| Missed namespace reference | Low | Medium | IDE refactoring + grep verification |
| Test failures | Low | Medium | Run tests before & after changes |
| Documentation inconsistency | Medium | Low | Systematic find/replace with verification |

**Critical Success Factors**:
1. ✅ composer validate --strict passes
2. ✅ All Pest tests pass (100% success)
3. ✅ PHPStan Level 5 analysis passes
4. ✅ Manual installation test in fresh Laravel project

---

## Ready for Implementation

**Planning Status**: ✅ COMPLETE

**Next Command**: `/speckit.tasks`

**Branch**: 002-package-rename
**Spec**: [spec.md](./spec.md)
**Plan**: [plan.md](./plan.md) ← You are here
**Research**: [research.md](./research.md)
**Data Model**: [data-model.md](./data-model.md)
**Quickstart**: [quickstart.md](./quickstart.md)
**Contracts**: [contracts/README.md](./contracts/README.md)

All planning artifacts complete. Feature ready for task generation and implementation.
