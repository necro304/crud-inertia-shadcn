---
description: "Implementation tasks for CRUD Generator Package"
---

# Tasks: CRUD Generator Package

**Input**: Design documents from `/specs/001-crud-generator-package/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/

**Tests**: Test-first development is REQUIRED (Constitution Principle III). All test tasks are mandatory.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Laravel Package**: `src/`, `config/`, `resources/stubs/`, `tests/` at repository root
- Following Spatie Laravel Package Tools conventions

---

## Phase 1: Setup (Package Infrastructure)

**Purpose**: Initialize Laravel package structure and tooling

- [ ] T001 Create Laravel package directory structure (src/, config/, resources/stubs/, tests/)
- [ ] T002 Initialize composer.json with Spatie Laravel Package Tools and dependencies
- [ ] T003 [P] Configure PHPStan Level 5 with phpstan.neon configuration file
- [ ] T004 [P] Configure Laravel Pint with pint.json for PSR-12 code style
- [ ] T005 [P] Configure Pest PHP with Pest.php and Orchestra Testbench setup
- [ ] T006 [P] Create .gitignore file for Laravel package (vendor/, .phpunit.cache, etc.)
- [ ] T007 Create README.md template with installation and basic usage instructions

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core utilities and service provider that ALL user stories depend on

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Tests for Foundational Components

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T008 [P] Unit test for NamingConverter in tests/Unit/NamingConverterTest.php
- [ ] T009 [P] Unit test for FieldDefinitionParser in tests/Unit/FieldDefinitionParserTest.php
- [ ] T010 [P] Unit test for ValidationRuleBuilder in tests/Unit/ValidationRuleBuilderTest.php
- [ ] T011 [P] Unit test for StubRenderer in tests/Unit/StubRendererTest.php
- [ ] T012 [P] Unit test for FileRollback in tests/Unit/FileRollbackTest.php

### Implementation of Foundational Components

- [ ] T013 [P] Implement NamingConverter utility in src/Support/NamingConverter.php (toPascalCase, toSnakeCase, toKebabCase, toPlural, toTableName, toRouteName)
- [ ] T014 [P] Implement FieldDefinitionParser in src/Parsers/FieldDefinitionParser.php (parse name:type:modifier syntax with regex validation)
- [ ] T015 [P] Implement ValidationRuleBuilder in src/Parsers/ValidationRuleBuilder.php (map field types to Laravel validation rules, handle :nullable/:unique modifiers)
- [ ] T016 [P] Implement StubRenderer in src/Support/StubRenderer.php (token replacement engine for {{ PLACEHOLDER }} syntax)
- [ ] T017 [P] Implement FileRollback manager in src/Support/FileRollback.php (track created files, rollback on exception, commit on success)
- [ ] T018 Create package configuration file in config/crud-generator.php (paths, namespaces, stubs location, defaults, field type mappings)
- [ ] T019 Implement CrudGeneratorServiceProvider in src/CrudGeneratorServiceProvider.php (register command, publish config/stubs using Spatie Package Tools)
- [ ] T020 [P] Create architectural test in tests/Arch/ArchTest.php (prevent dd/dump/ray functions in package source code)

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Generate Basic CRUD with Single Command (Priority: P1) 🎯 MVP

**Goal**: Developer runs `php artisan make:crud Product name:string price:decimal` and receives 9 production-ready files following the established pattern

**Independent Test**: Install package via Composer, optionally publish stubs, run command with resource name and field definitions, verify all 9 files are created with correct content and naming

### Tests for User Story 1

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T021 [P] [US1] Feature test for basic CRUD generation in tests/Feature/CrudGenerationTest.php (test 9 files created, correct naming, basic content)
- [ ] T022 [P] [US1] Feature test for field type support in tests/Feature/FieldTypeSupportTest.php (string, text, integer, decimal, boolean, date, datetime, timestamp, json)
- [ ] T023 [P] [US1] Feature test for field modifiers in tests/Feature/FieldModifiersTest.php (test :nullable, :unique, combined modifiers)
- [ ] T024 [P] [US1] Feature test for naming conventions in tests/Feature/NamingConventionTest.php (PascalCase → snake_case → kebab-case transformations)
- [ ] T025 [P] [US1] Feature test for atomic rollback in tests/Feature/AtomicRollbackTest.php (test failure triggers deletion of all created files)
- [ ] T026 [P] [US1] Feature test for command validation in tests/Feature/CommandValidationTest.php (invalid resource names, invalid field types, reserved words)

### Stub Templates for User Story 1

- [ ] T027 [P] [US1] Create Model stub template in resources/stubs/model.stub (SoftDeletes, Auditable, fillable, casts, table name)
- [ ] T028 [P] [US1] Create Controller stub template in resources/stubs/controller.stub (QueryBuilder, Inertia, 7 RESTful methods, Form Requests)
- [ ] T029 [P] [US1] Create StoreRequest stub template in resources/stubs/store-request.stub (validation rules from field definitions)
- [ ] T030 [P] [US1] Create UpdateRequest stub template in resources/stubs/update-request.stub (validation rules with unique ignore for current record)
- [ ] T031 [P] [US1] Create Resource stub template in resources/stubs/resource.stub (API transformation with all fields, timestamps as ISO strings)
- [ ] T032 [P] [US1] Create Index.vue stub template in resources/stubs/index.vue.stub (TypeScript, DataTable, search/filter/sort, delete confirmation)
- [ ] T033 [P] [US1] Create Create.vue stub template in resources/stubs/create.vue.stub (TypeScript, Inertia form, reusable Form component)
- [ ] T034 [P] [US1] Create Edit.vue stub template in resources/stubs/edit.vue.stub (TypeScript, Inertia form, reusable Form component)
- [ ] T035 [P] [US1] Create Form.vue stub template in resources/stubs/form.vue.stub (TypeScript, Shadcn-vue components, field-specific inputs, error display)
- [ ] T036 [P] [US1] Create Migration stub template in resources/stubs/migration.stub (table creation, field columns with types/modifiers, foreign keys, indexes)

### Generators for User Story 1

- [ ] T037 [P] [US1] Implement ModelGenerator in src/Generators/ModelGenerator.php (load model.stub, render with resource tokens, handle --no-soft-deletes/--no-auditing flags)
- [ ] T038 [P] [US1] Implement ControllerGenerator in src/Generators/ControllerGenerator.php (load controller.stub, render with resource tokens, handle QueryBuilder patterns)
- [ ] T039 [P] [US1] Implement RequestGenerator in src/Generators/RequestGenerator.php (load request stubs, render with validation rules from ValidationRuleBuilder)
- [ ] T040 [P] [US1] Implement ResourceGenerator in src/Generators/ResourceGenerator.php (load resource.stub, render with field list from field definitions)
- [ ] T041 [P] [US1] Implement VueGenerator in src/Generators/VueGenerator.php (load vue stubs, render with TypeScript interfaces, handle field-specific input components)
- [ ] T042 [P] [US1] Implement MigrationGenerator in src/Generators/MigrationGenerator.php (load migration.stub, render with table name, field columns, indexes, foreign keys)

### Main Command for User Story 1

- [ ] T043 [US1] Implement MakeCrudCommand in src/Commands/MakeCrudCommand.php (argument/option parsing, orchestrate generators including MigrationGenerator, atomic generation with FileRollback, output levels)
- [ ] T044 [US1] Add command signature and options (resource, fields, --no-soft-deletes, --no-auditing, --no-views, --table, --force, --quiet, --verbose)
- [ ] T045 [US1] Implement resource name validation (alphanumeric, starts with letter, 1-50 chars, reserved word check)
- [ ] T046 [US1] Implement field definition validation (use FieldDefinitionParser, validate types and modifiers)
- [ ] T047 [US1] Implement atomic generation flow (FileRollback tracking, try-catch, rollback on exception, commit on success)
- [ ] T048 [US1] Implement output levels (normal with progress indicators, --quiet for errors only, --verbose for detailed logging)
- [ ] T049 [US1] Add console output messages (success summary with file paths including migration, error messages with actionable guidance, rollback notifications)

### Quality Validation for User Story 1

- [ ] T050 [US1] Feature test for generated code quality in tests/Feature/GeneratedCodeQualityTest.php (run PHPStan Level 5 on generated PHP, run TypeScript compiler on generated Vue)
- [ ] T051 [US1] Integration test for complete user journey in tests/Feature/CompleteUserJourneyTest.php (install package → publish stubs → run command → verify 10 files (including migration) → check quality gates)

**Checkpoint**: At this point, User Story 1 should be fully functional - developers can generate complete CRUD modules with a single command (10 files total)

---

## Phase 4: User Story 2 - Customize Generated Code with Options (Priority: P2)

**Goal**: Developer can customize CRUD generation to match project-specific requirements by passing options (--no-soft-deletes, --no-auditing, --no-views, --table=custom_name)

**Independent Test**: Run commands with different flag combinations and verify generated code reflects those options

### Tests for User Story 2

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T052 [P] [US2] Feature test for --no-soft-deletes flag in tests/Feature/CommandOptionsTest.php (verify Model lacks SoftDeletes trait, migration lacks softDeletes column)
- [ ] T053 [P] [US2] Feature test for --no-auditing flag in tests/Feature/CommandOptionsTest.php (verify Model doesn't implement Auditable contract)
- [ ] T054 [P] [US2] Feature test for --no-views flag in tests/Feature/CommandOptionsTest.php (verify only 6 backend files generated including migration, no Vue files)
- [ ] T055 [P] [US2] Feature test for --table option in tests/Feature/CommandOptionsTest.php (verify Model specifies custom table name in protected $table property)
- [ ] T056 [P] [US2] Feature test for --force flag in tests/Feature/CommandOptionsTest.php (verify existing files are overwritten without confirmation)

### Implementation for User Story 2

- [ ] T057 [US2] Update ModelGenerator to handle --no-soft-deletes flag in src/Generators/ModelGenerator.php (conditionally include SoftDeletes trait and imports)
- [ ] T058 [US2] Update ModelGenerator to handle --no-auditing flag in src/Generators/ModelGenerator.php (conditionally include Auditable contract and trait)
- [ ] T059 [US2] Update ModelGenerator to handle --table option in src/Generators/ModelGenerator.php (conditionally add protected $table property)
- [ ] T060 [US2] Update MakeCrudCommand to handle --no-views flag in src/Commands/MakeCrudCommand.php (skip VueGenerator when flag present)
- [ ] T061 [US2] Update MakeCrudCommand to handle --force flag in src/Commands/MakeCrudCommand.php (skip file existence check when force=true)
- [ ] T062 [US2] Update command option validation in src/Commands/MakeCrudCommand.php (validate custom table name format, check quiet/verbose mutual exclusivity)

**Checkpoint**: At this point, User Stories 1 AND 2 should both work - developers can generate customized CRUD modules with flexible options

---

## Phase 5: User Story 3 - Define Relationships in Generated Code (Priority: P3)

**Goal**: Developer can define relationships (belongsTo, hasMany, hasOne, morphMany) when generating CRUD so Models include relationship methods and Controllers eager load them automatically

**Independent Test**: Run command with relationship flags and verify Model methods and Controller eager loading are generated

### Tests for User Story 3

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T063 [P] [US3] Feature test for --belongs-to option in tests/Feature/RelationshipGenerationTest.php (verify Model includes belongsTo method, migration adds foreign key, Controller eager loads)
- [ ] T064 [P] [US3] Feature test for --has-many option in tests/Feature/RelationshipGenerationTest.php (verify Model includes hasMany method, Controller eager loads)
- [ ] T065 [P] [US3] Feature test for --has-one option in tests/Feature/RelationshipGenerationTest.php (verify Model includes hasOne method, Controller eager loads)
- [ ] T066 [P] [US3] Feature test for --morph-many option in tests/Feature/RelationshipGenerationTest.php (verify Model includes morphMany method, migration creates morphable columns)
- [ ] T067 [P] [US3] Feature test for multiple relationships in tests/Feature/RelationshipGenerationTest.php (verify all relationships included, proper eager loading syntax)

### Implementation for User Story 3

- [ ] T068 [P] [US3] Implement RelationshipParser in src/Parsers/RelationshipParser.php (parse --belongs-to=User,Category syntax, create RelationshipDefinition entities)
- [ ] T069 [US3] Update ModelGenerator to include relationship methods in src/Generators/ModelGenerator.php (generate belongsTo, hasMany, hasOne, morphMany methods from RelationshipDefinition)
- [ ] T070 [US3] Update ControllerGenerator to add eager loading in src/Generators/ControllerGenerator.php (add ->with(['relationship']) to QueryBuilder in index method)
- [ ] T071 [US3] Update MakeCrudCommand to handle relationship options in src/Commands/MakeCrudCommand.php (parse --belongs-to, --has-many, --has-one, --morph-many flags)
- [ ] T072 [US3] Update model.stub to support relationship methods in resources/stubs/model.stub (add {{ RELATIONSHIPS }} token placeholder)
- [ ] T073 [US3] Update controller.stub to support eager loading in resources/stubs/controller.stub (add {{ EAGER_LOAD }} token to QueryBuilder)

**Checkpoint**: All user stories should now be independently functional - complete CRUD generation with full customization and relationship support

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Quality assurance, documentation, and cross-story improvements

### Documentation and Examples

- [ ] T074 [P] Create comprehensive README.md with installation, usage examples, all command options, and troubleshooting
- [ ] T075 [P] Create CHANGELOG.md following Keep a Changelog format (version 1.0.0 initial release)
- [ ] T076 [P] Create CONTRIBUTING.md with development setup and testing guidelines
- [ ] T077 [P] Add inline PHPDoc comments to all public methods in src/ directory
- [ ] T078 [P] Create example project documentation showing generated CRUD in action

### Code Quality and Cleanup

- [ ] T079 Run PHPStan Level 5 analysis on entire src/ and config/ directories (composer analyse)
- [ ] T080 Run Laravel Pint formatting on entire codebase (composer format)
- [ ] T081 Run full Pest test suite with coverage report (composer test-coverage)
- [ ] T082 [P] Review and refactor duplicate code across generators (extract common patterns)
- [ ] T083 [P] Optimize stub rendering performance (cache loaded stubs, optimize token replacement)
- [ ] T084 Validate all stub templates have valid PHP/Vue syntax (run php -l on PHP stubs, tsc on Vue stubs)

### Final Validation

- [ ] T085 Execute quickstart.md validation checklist (all functional requirements implemented, all acceptance scenarios tested, all edge cases handled)
- [ ] T086 Verify Constitution compliance (re-run Constitution Check from plan.md, confirm all 7 principles satisfied)
- [ ] T087 Verify Success Criteria achievement (SC-001: <30s generation, SC-002: code quality gates, SC-003: 90% no manual edits, SC-004: 80% time reduction)
- [ ] T088 Test package installation in fresh Laravel 12 project (composer require, vendor:publish, make:crud command)
- [ ] T089 Generate sample CRUDs for Product, User, Category to validate pattern consistency
- [ ] T090 Run generated code through PHPStan Level 5 and Laravel Pint to verify SC-002
- [ ] T091 Performance test: measure generation time for complex CRUD with 10 fields and relationships

### Packaging and Release

- [ ] T092 [P] Create LICENSE file (choose appropriate open source license)
- [ ] T093 [P] Create .gitattributes for package export optimization
- [ ] T094 Tag version 1.0.0 in git repository
- [ ] T095 Prepare Packagist submission (verify composer.json metadata, description, keywords)

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3-5)**: All depend on Foundational phase completion
  - User stories can proceed in parallel (if staffed) since they're independent
  - Or sequentially in priority order (US1 → US2 → US3)
- **Polish (Phase 6)**: Depends on all user stories being complete

### User Story Dependencies

- **User Story 1 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 2 (P2)**: Can start after Foundational (Phase 2) - Builds on US1 generators but independently testable
- **User Story 3 (P3)**: Can start after Foundational (Phase 2) - Extends US1/US2 but independently testable

### Within Each User Story

- Tests MUST be written and FAIL before implementation (Test-First Development)
- Stubs before generators (generators load stubs)
- Generators before command integration (command orchestrates generators)
- Basic functionality before quality validation
- Story complete before moving to next priority

### Parallel Opportunities

#### Setup Phase
```bash
# These can run in parallel:
T003 (PHPStan config), T004 (Pint config), T005 (Pest config), T006 (gitignore)
```

#### Foundational Tests Phase
```bash
# All foundational tests can run in parallel (T008-T012):
Task: "Unit test for NamingConverter"
Task: "Unit test for FieldDefinitionParser"
Task: "Unit test for ValidationRuleBuilder"
Task: "Unit test for StubRenderer"
Task: "Unit test for FileRollback"
```

#### Foundational Implementation Phase
```bash
# All foundational utilities can run in parallel (T013-T017, T020):
Task: "Implement NamingConverter"
Task: "Implement FieldDefinitionParser"
Task: "Implement ValidationRuleBuilder"
Task: "Implement StubRenderer"
Task: "Implement FileRollback"
Task: "Create architectural test"
```

#### User Story 1 Tests
```bash
# All US1 tests can run in parallel (T021-T026):
Task: "Feature test for basic CRUD generation"
Task: "Feature test for field type support"
Task: "Feature test for field modifiers"
Task: "Feature test for naming conventions"
Task: "Feature test for atomic rollback"
Task: "Feature test for command validation"
```

#### User Story 1 Stubs
```bash
# All US1 stubs can run in parallel (T027-T035):
Task: "Create Model stub template"
Task: "Create Controller stub template"
Task: "Create StoreRequest stub template"
Task: "Create UpdateRequest stub template"
Task: "Create Resource stub template"
Task: "Create Index.vue stub template"
Task: "Create Create.vue stub template"
Task: "Create Edit.vue stub template"
Task: "Create Form.vue stub template"
```

#### User Story 1 Generators
```bash
# All US1 generators can run in parallel (T036-T040):
Task: "Implement ModelGenerator"
Task: "Implement ControllerGenerator"
Task: "Implement RequestGenerator"
Task: "Implement ResourceGenerator"
Task: "Implement VueGenerator"
```

#### User Story 2 Tests
```bash
# All US2 tests can run in parallel (T050-T054):
Task: "Feature test for --no-soft-deletes flag"
Task: "Feature test for --no-auditing flag"
Task: "Feature test for --no-views flag"
Task: "Feature test for --table option"
Task: "Feature test for --force flag"
```

#### User Story 3 Tests
```bash
# All US3 tests can run in parallel (T061-T065):
Task: "Feature test for --belongs-to option"
Task: "Feature test for --has-many option"
Task: "Feature test for --has-one option"
Task: "Feature test for --morph-many option"
Task: "Feature test for multiple relationships"
```

#### Polish Phase Documentation
```bash
# Documentation tasks can run in parallel (T072-T076):
Task: "Create comprehensive README.md"
Task: "Create CHANGELOG.md"
Task: "Create CONTRIBUTING.md"
Task: "Add inline PHPDoc comments"
Task: "Create example project documentation"
```

#### Polish Phase Code Quality
```bash
# Code quality tasks can run in parallel (T080-T082):
Task: "Review and refactor duplicate code"
Task: "Optimize stub rendering performance"
Task: "Validate all stub templates have valid syntax"
```

---

## Parallel Example: User Story 1

```bash
# Phase 1: Launch all tests together (T021-T026)
Task: "Feature test for basic CRUD generation in tests/Feature/CrudGenerationTest.php"
Task: "Feature test for field type support in tests/Feature/FieldTypeSupportTest.php"
Task: "Feature test for field modifiers in tests/Feature/FieldModifiersTest.php"
Task: "Feature test for naming conventions in tests/Feature/NamingConventionTest.php"
Task: "Feature test for atomic rollback in tests/Feature/AtomicRollbackTest.php"
Task: "Feature test for command validation in tests/Feature/CommandValidationTest.php"

# Phase 2: Launch all stub templates together (T027-T036)
Task: "Create Model stub template in resources/stubs/model.stub"
Task: "Create Controller stub template in resources/stubs/controller.stub"
Task: "Create StoreRequest stub template in resources/stubs/store-request.stub"
Task: "Create UpdateRequest stub template in resources/stubs/update-request.stub"
Task: "Create Resource stub template in resources/stubs/resource.stub"
Task: "Create Index.vue stub template in resources/stubs/index.vue.stub"
Task: "Create Create.vue stub template in resources/stubs/create.vue.stub"
Task: "Create Edit.vue stub template in resources/stubs/edit.vue.stub"
Task: "Create Form.vue stub template in resources/stubs/form.vue.stub"
Task: "Create Migration stub template in resources/stubs/migration.stub"

# Phase 3: Launch all generators together (T037-T042)
Task: "Implement ModelGenerator in src/Generators/ModelGenerator.php"
Task: "Implement ControllerGenerator in src/Generators/ControllerGenerator.php"
Task: "Implement RequestGenerator in src/Generators/RequestGenerator.php"
Task: "Implement ResourceGenerator in src/Generators/ResourceGenerator.php"
Task: "Implement VueGenerator in src/Generators/VueGenerator.php"
Task: "Implement MigrationGenerator in src/Generators/MigrationGenerator.php"

# Phase 4: Command implementation (sequential - T043-T051)
# These depend on generators being complete, so run sequentially
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup (T001-T007)
2. Complete Phase 2: Foundational (T008-T020) - CRITICAL: blocks all stories
3. Complete Phase 3: User Story 1 (T021-T051)
4. **STOP and VALIDATE**: Test US1 independently, verify 10 files generated correctly
5. Deploy/demo if ready - developers can now generate basic CRUDs!

**Estimated Time**: 8-10 hours for MVP (as specified in quickstart.md)

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready (T001-T020)
2. Add User Story 1 → Test independently → Deploy/Demo (T021-T051) - **MVP Release!**
3. Add User Story 2 → Test independently → Deploy/Demo (T052-T062) - Customization features
4. Add User Story 3 → Test independently → Deploy/Demo (T063-T073) - Relationship support
5. Polish and Release (T074-T095) - Production-ready package
6. Each story adds value without breaking previous stories

### Parallel Team Strategy

With multiple developers:

1. **Team completes Setup + Foundational together** (T001-T020)
2. **Once Foundational is done, split work**:
   - Developer A: User Story 1 (T021-T051) - Basic CRUD generation
   - Developer B: User Story 2 (T052-T062) - Command options
   - Developer C: User Story 3 (T063-T073) - Relationships
3. Stories complete and integrate independently
4. Team reconvenes for Polish phase (T074-T095)

---

## Notes

- **[P] tasks** = different files, no dependencies, can run in parallel
- **[Story] label** maps task to specific user story for traceability
- Each user story should be independently completable and testable
- **Test-First Development is MANDATORY** (Constitution Principle III)
- Verify tests fail before implementing (red-green-refactor cycle)
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- **Quality gates**: PHPStan Level 5, Laravel Pint, Pest tests must pass
- Avoid: vague tasks, same file conflicts, cross-story dependencies that break independence

---

## Task Count Summary

- **Total Tasks**: 95
- **Setup (Phase 1)**: 7 tasks
- **Foundational (Phase 2)**: 13 tasks (5 tests + 8 implementation)
- **User Story 1 (Phase 3)**: 31 tasks (6 tests + 10 stubs + 6 generators + 7 command + 2 quality)
- **User Story 2 (Phase 4)**: 11 tasks (5 tests + 6 implementation)
- **User Story 3 (Phase 5)**: 11 tasks (5 tests + 6 implementation)
- **Polish (Phase 6)**: 22 tasks (5 documentation + 6 quality + 7 validation + 4 release)

**Parallel Opportunities**: 50 tasks marked [P] can run in parallel within their phases

**MVP Scope**: Phase 1 + Phase 2 + Phase 3 (51 tasks) delivers basic CRUD generation

**Estimated MVP Time**: 8-10 hours (per quickstart.md estimates)
