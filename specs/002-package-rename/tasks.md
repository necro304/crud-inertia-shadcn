# Tasks: Package Rename to necro304/crud-inertia-shadcn

**Input**: Design documents from `/specs/002-package-rename/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/, quickstart.md

**Tests**: No test tasks included - this is a metadata rename feature with validation through existing Pest test suite.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

Laravel package structure at repository root:
- `composer.json` - Package metadata
- `README.md` - Installation documentation
- `CLAUDE.md` - AI agent context
- `example/CLAUDE.md` - Example project context
- `configure.php` - Configuration script
- `src/` - Package source code
- `tests/` - Pest PHP test suite

---

## Phase 1: Setup (Pre-Flight Verification)

**Purpose**: Verify current state before making changes

- [ ] T001 Verify clean git working directory with `git status`
- [ ] T002 Verify current branch is 002-package-rename with `git branch`
- [ ] T003 [P] Run existing Pest test suite to establish baseline with `composer test`
- [ ] T004 [P] Run PHPStan analysis to establish baseline with `composer analyse`
- [ ] T005 [P] Run Laravel Pint to ensure code style compliance with `composer format`

**Checkpoint**: All baseline checks pass - ready to begin rename

---

## Phase 2: Foundational (No Blocking Prerequisites)

**Purpose**: This feature has NO foundational blocking tasks - metadata rename can proceed directly to user stories

**⚠️ NOTE**: Skipping foundational phase - no shared infrastructure needed for metadata changes

**Checkpoint**: Foundation ready (N/A) - user story implementation can begin immediately

---

## Phase 3: User Story 1 - Package Installation with New Name (Priority: P1) 🎯 MVP

**Goal**: Update composer.json and PHP namespaces so the package can be installed as `necro304/crud-inertia-shadcn` with correct autoloading

**Independent Test**: Run `composer require necro304/crud-inertia-shadcn` in a fresh Laravel project and verify successful installation with `php artisan list` showing package commands

### Implementation for User Story 1

- [ ] T006 [US1] Identify current package name and namespace in composer.json
- [ ] T007 [US1] Update composer.json `name` field to `necro304/crud-inertia-shadcn`
- [ ] T008 [P] [US1] Update composer.json `description` field to `Laravel package for generating CRUD with Inertia.js and Shadcn UI`
- [ ] T009 [P] [US1] Update composer.json `keywords` array to include `laravel`, `crud`, `inertia`, `vue`, `shadcn`, `generator`, `package`
- [ ] T010 [P] [US1] Update composer.json `homepage` to `https://github.com/necro304/crud-inertia-shadcn`
- [ ] T011 [P] [US1] Update composer.json `license` to `MIT` (if not already set)
- [ ] T012 [P] [US1] Update composer.json `authors` array with necro304 author information
- [ ] T013 [US1] Update composer.json `autoload.psr-4` key from current namespace to `Necro304\\CrudInertiaShadcn\\`
- [ ] T014 [US1] Update composer.json `autoload-dev.psr-4` key from current namespace to `Necro304\\CrudInertiaShadcn\\Tests\\`
- [ ] T015 [P] [US1] Update composer.json `extra.laravel.providers` array to reference `Necro304\\CrudInertiaShadcn\\CrudGeneratorServiceProvider`
- [ ] T016 [P] [US1] Update composer.json `support.issues` to `https://github.com/necro304/crud-inertia-shadcn/issues`
- [ ] T017 [P] [US1] Update composer.json `support.source` to `https://github.com/necro304/crud-inertia-shadcn`
- [ ] T018 [US1] Run `composer validate --strict` to verify composer.json syntax and package metadata
- [ ] T019 [US1] Update namespace declaration in src/CrudGeneratorServiceProvider.php to `Necro304\CrudInertiaShadcn`
- [ ] T020 [P] [US1] Update namespace declarations in all src/Commands/*.php files to `Necro304\CrudInertiaShadcn\Commands`
- [ ] T021 [P] [US1] Update namespace declarations in all src/Facades/*.php files to `Necro304\CrudInertiaShadcn\Facades` (if directory exists)
- [ ] T022 [P] [US1] Update namespace declarations in all src/Support/*.php files to `Necro304\CrudInertiaShadcn\Support`
- [ ] T023 [P] [US1] Update namespace declaration in tests/TestCase.php to `Necro304\CrudInertiaShadcn\Tests`
- [ ] T024 [P] [US1] Update namespace declaration in tests/Pest.php to reference `Necro304\CrudInertiaShadcn\Tests\TestCase`
- [ ] T025 [P] [US1] Update namespace declarations in all tests/*Test.php files to `Necro304\CrudInertiaShadcn\Tests`
- [ ] T026 [US1] Update all `use` statements in src/ files to reference `Necro304\CrudInertiaShadcn\` namespace
- [ ] T027 [US1] Update all `use` statements in tests/ files to reference `Necro304\CrudInertiaShadcn\` namespace
- [ ] T028 [US1] Run `composer dump-autoload -o` to regenerate optimized autoloader
- [ ] T029 [US1] Run `composer test` to verify all Pest tests pass with new namespace
- [ ] T030 [US1] Run `composer analyse` to verify PHPStan Level 5 passes with no namespace errors
- [ ] T031 [US1] Test package installation in temporary Laravel project: create project, add local path repository, require package, verify `php artisan list` shows commands

**Checkpoint**: Package can be successfully installed with new name and all functionality works

---

## Phase 4: User Story 2 - Updated Package Documentation (Priority: P2)

**Goal**: Update README.md and CLAUDE.md files to reflect new package name, installation commands, and repository URLs

**Independent Test**: Review all documentation files and verify zero references to old package name using `grep -r "old-package-name" README.md CLAUDE.md example/`

### Implementation for User Story 2

- [ ] T032 [P] [US2] Update README.md title/heading to reference `necro304/crud-inertia-shadcn`
- [ ] T033 [P] [US2] Update README.md installation command to `composer require necro304/crud-inertia-shadcn`
- [ ] T034 [P] [US2] Update README.md package description to match composer.json description
- [ ] T035 [P] [US2] Update README.md repository URLs/badges to point to `https://github.com/necro304/crud-inertia-shadcn`
- [ ] T036 [P] [US2] Update README.md author references to `necro304`
- [ ] T037 [P] [US2] Update README.md Packagist badges (version, downloads, license) to reference `necro304/crud-inertia-shadcn`
- [ ] T038 [P] [US2] Update CLAUDE.md "Project Overview" section with package name `necro304/crud-inertia-shadcn`
- [ ] T039 [P] [US2] Add entry to CLAUDE.md "Recent Changes" section documenting package rename to necro304/crud-inertia-shadcn
- [ ] T040 [P] [US2] Update CLAUDE.md "Package Configuration" section to reference correct package name in configure.php instructions
- [ ] T041 [P] [US2] Update example/CLAUDE.md with any package name references to `necro304/crud-inertia-shadcn`
- [ ] T042 [US2] Search all documentation files for old package name placeholders with `grep -ri ":vendor_slug\|:package_slug" README.md CLAUDE.md example/`
- [ ] T043 [US2] Verify all documentation uses consistent `necro304/crud-inertia-shadcn` package name with no variations

**Checkpoint**: All documentation accurately reflects new package name and installation instructions

---

## Phase 5: User Story 3 - Configuration Script Updates (Priority: P3)

**Goal**: Update configure.php script to use new package name as defaults and examples for interactive setup

**Independent Test**: Run `php configure.php` interactively (or with defaults) and verify prompts show `necro304/crud-inertia-shadcn` as example/default values

### Implementation for User Story 3

- [ ] T044 [P] [US3] Update configure.php vendor name default to `Necro304` in `ask('Vendor name', 'Necro304')`
- [ ] T045 [P] [US3] Update configure.php vendor slug default to `necro304` in `ask('Vendor slug', 'necro304')`
- [ ] T046 [P] [US3] Update configure.php package name default to `Crud Inertia Shadcn` in `ask('Package name', 'Crud Inertia Shadcn')`
- [ ] T047 [P] [US3] Update configure.php package slug default to `crud-inertia-shadcn` in `ask('Package slug', 'crud-inertia-shadcn')`
- [ ] T048 [P] [US3] Update configure.php package description default to `Laravel package for generating CRUD with Inertia.js and Shadcn UI`
- [ ] T049 [US3] Update configure.php author username default to `necro304` (if applicable)
- [ ] T050 [US3] Test configure.php by running it and pressing Enter through all prompts to verify defaults are correct
- [ ] T051 [US3] Verify configure.php handles already-configured package gracefully (idempotency check)

**Checkpoint**: Configuration script guides users to correct package name format

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Final validation and cleanup affecting all user stories

- [ ] T052 [P] Run full Pest test suite one final time with `composer test` - verify 100% pass rate
- [ ] T053 [P] Run PHPStan Level 5 analysis with `composer analyse` - verify zero errors
- [ ] T054 [P] Run Laravel Pint code style check with `composer format` - verify no style violations
- [ ] T055 [P] Validate composer.json package metadata with `composer validate --strict --no-check-lock`
- [ ] T056 Search entire codebase for old package name references with `grep -ri "old-package\|placeholder" .` (excluding .git, vendor, node_modules)
- [ ] T057 Review quickstart.md implementation guide to verify all steps completed
- [ ] T058 Create comprehensive git commit message documenting all package rename changes
- [ ] T059 Run git diff to review all changes before committing
- [ ] T060 Stage all changed files with `git add composer.json src/ tests/ README.md CLAUDE.md example/CLAUDE.md configure.php`
- [ ] T061 Commit changes with descriptive message and Co-Authored-By: Claude footer
- [ ] T062 Push changes to remote branch with `git push origin 002-package-rename`

**Checkpoint**: All changes validated, committed, and pushed to remote

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: SKIPPED - No blocking prerequisites for metadata rename
- **User Story 1 (Phase 3)**: Can start immediately after Setup - Core package identity changes
- **User Story 2 (Phase 4)**: Depends on US1 completion (needs correct package name from composer.json)
- **User Story 3 (Phase 5)**: Can run in parallel with US2 (independent files)
- **Polish (Phase 6)**: Depends on all user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: No dependencies - establishes package identity
- **User Story 2 (P2)**: Soft dependency on US1 (documentation references composer.json package name)
- **User Story 3 (P3)**: No dependencies on other stories - independent script updates

### Within Each User Story

**User Story 1** (composer.json + namespaces):
1. Update composer.json fields (T006-T018) - many can run in parallel [P]
2. Validate composer.json (T018) - MUST pass before namespace changes
3. Update namespace declarations (T019-T027) - many can run in parallel [P]
4. Regenerate autoloader (T028) - depends on namespace changes
5. Run validation tests (T029-T031) - sequential verification

**User Story 2** (documentation):
- All documentation tasks (T032-T043) can run in parallel [P] - different files

**User Story 3** (configure.php):
- All configure.php updates (T044-T051) can run in parallel [P] - same file but independent variable updates

### Parallel Opportunities

- **Setup Phase**: T003, T004, T005 can run in parallel (different commands)
- **User Story 1**:
  - T008-T012, T016-T017 (composer.json fields) - parallel
  - T020-T025 (namespace declarations) - parallel
- **User Story 2**: T032-T041 (all documentation updates) - parallel
- **User Story 3**: T044-T049 (all configure.php defaults) - parallel
- **Polish Phase**: T052-T055 (validation commands) - parallel

**User Stories 2 and 3 can run in parallel** (different files: documentation vs configure.php)

---

## Parallel Example: User Story 1

```bash
# Update composer.json fields together:
Task: "Update composer.json description field"
Task: "Update composer.json keywords array"
Task: "Update composer.json homepage"
Task: "Update composer.json license"
Task: "Update composer.json authors array"

# Then after composer validate passes:
# Update namespace declarations together:
Task: "Update namespace in all src/Commands/*.php files"
Task: "Update namespace in all src/Facades/*.php files"
Task: "Update namespace in all src/Support/*.php files"
Task: "Update namespace in tests/TestCase.php"
Task: "Update namespace in all tests/*Test.php files"
```

---

## Parallel Example: User Story 2

```bash
# All documentation updates together:
Task: "Update README.md title/heading"
Task: "Update README.md installation command"
Task: "Update README.md repository URLs/badges"
Task: "Update CLAUDE.md Project Overview section"
Task: "Update CLAUDE.md Recent Changes section"
Task: "Update example/CLAUDE.md references"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup (5 min)
2. Skip Phase 2: Foundational (no blocking prerequisites)
3. Complete Phase 3: User Story 1 (25-30 min)
   - Update composer.json
   - Update PHP namespaces
   - Validate with tests
4. **STOP and VALIDATE**: Test package installation independently
5. Ready for use with new package name

**Estimated Time for MVP**: 30-35 minutes

### Incremental Delivery

1. Complete Setup → Ready to modify
2. Add User Story 1 → Test independently → **Package installable as necro304/crud-inertia-shadcn** (MVP!)
3. Add User Story 2 → Test independently → **Documentation updated**
4. Add User Story 3 → Test independently → **Configure script updated**
5. Polish phase → **Production ready**

### Parallel Team Strategy

With multiple developers:

1. Developer A: Complete Setup + User Story 1 (MUST finish first)
2. Once US1 complete:
   - Developer B: User Story 2 (documentation)
   - Developer C: User Story 3 (configure.php)
3. Developer A: Polish phase after US2 + US3 complete

**Recommendation**: Single developer, sequential execution (30-45 min total) - complexity is low enough that parallelization adds overhead without time savings

---

## Task Summary

**Total Tasks**: 62
- Setup: 5 tasks
- Foundational: 0 tasks (skipped)
- User Story 1 (P1): 26 tasks (composer.json + namespaces) 🎯 MVP
- User Story 2 (P2): 12 tasks (documentation)
- User Story 3 (P3): 8 tasks (configure.php)
- Polish: 11 tasks (validation + git)

**Parallel Opportunities**:
- 15 tasks marked [P] in User Story 1
- 10 tasks marked [P] in User Story 2
- 6 tasks marked [P] in User Story 3
- 4 tasks marked [P] in Polish phase
- **Total parallelizable**: 35 tasks (56%)

**Critical Path** (sequential dependencies):
1. T001-T005 (Setup)
2. T006-T018 (composer.json updates + validation)
3. T019-T028 (namespace updates + autoload regeneration)
4. T029-T031 (validation tests)
5. T032-T043 (documentation - can overlap with T044-T051)
6. T044-T051 (configure.php - can overlap with T032-T043)
7. T052-T062 (final validation + git commit)

**MVP Scope**: User Story 1 only (26 tasks, ~30 minutes)
**Full Feature**: All 3 user stories (46 implementation tasks + 16 validation tasks, ~45 minutes)

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story is independently completable and testable
- No test generation needed - existing Pest suite validates rename
- Commit after completing each user story phase
- Stop at any checkpoint to validate story independently
- Use IDE refactoring tools for namespace changes to speed up US1
- Validation tasks (composer validate, composer test, composer analyse) are critical - MUST pass
