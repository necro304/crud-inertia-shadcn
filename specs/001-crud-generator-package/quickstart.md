# Quickstart: CRUD Generator Package

**Date**: 2025-01-13
**For**: Developers implementing the CRUD Generator Package
**Estimated Time**: 15 minutes to understand, 2-3 hours for first implementation

## Prerequisites

Before starting implementation, ensure you have:

- ✅ Read [spec.md](spec.md) - Feature specification
- ✅ Read [plan.md](plan.md) - Implementation plan
- ✅ Read [research.md](research.md) - Technical decisions
- ✅ Read [data-model.md](data-model.md) - Domain entities
- ✅ Read [contracts/](contracts/) - API contracts
- ✅ PHP 8.4+ installed
- ✅ Composer installed
- ✅ Familiarity with Laravel 12 package development
- ✅ Understanding of Spatie Laravel Package Tools

## Package Setup (5 minutes)

### 1. Install Dependencies

```bash
# Core Laravel package development
composer require spatie/laravel-package-tools --dev

# Development and testing
composer require orchestra/testbench --dev
composer require pestphp/pest --dev
composer require pestphp/pest-plugin-laravel --dev
composer require larastan/larastan --dev
composer require laravel/pint --dev
```

### 2. Configure Composer

Update `composer.json`:

```json
{
  "name": "vendor/crud-generator",
  "description": "Laravel CRUD generator with Vue 3 + Inertia.js + Shadcn-vue",
  "type": "library",
  "require": {
    "php": "^8.4|^8.3",
    "illuminate/support": "^12.0",
    "spatie/laravel-package-tools": "^1.16"
  },
  "require-dev": {
    "orchestra/testbench": "^10.0",
    "pestphp/pest": "^3.0",
    "pestphp/pest-plugin-laravel": "^3.0",
    "larastan/larastan": "^3.0",
    "laravel/pint": "^1.13"
  },
  "autoload": {
    "psr-4": {
      "Vendor\\CrudGenerator\\": "src/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "Vendor\\CrudGenerator\\Tests\\": "tests/"
    }
  },
  "scripts": {
    "test": "vendor/bin/pest",
    "test-coverage": "vendor/bin/pest --coverage",
    "analyse": "vendor/bin/phpstan analyse",
    "format": "vendor/bin/pint"
  },
  "extra": {
    "laravel": {
      "providers": [
        "Vendor\\CrudGenerator\\CrudGeneratorServiceProvider"
      ]
    }
  }
}
```

### 3. Create Directory Structure

```bash
mkdir -p src/Commands
mkdir -p src/Generators
mkdir -p src/Parsers
mkdir -p src/Support
mkdir -p config
mkdir -p resources/stubs
mkdir -p tests/Arch
mkdir -p tests/Feature
mkdir -p tests/Unit
```

## Implementation Order (Test-First)

Follow **Test-First Development** (Constitution Principle III):

### Phase 1: Core Utilities (1 hour)

**Test → Implement → Validate**

1. **NamingConverter** (tests/Unit/NamingConverterTest.php → src/Support/NamingConverter.php)
   - Test PascalCase conversion
   - Test snake_case conversion
   - Test kebab-case conversion
   - Test pluralization
   - Implement using Laravel's `Str` helper

2. **FieldDefinitionParser** (tests/Unit/FieldDefinitionParserTest.php → src/Parsers/FieldDefinitionParser.php)
   - Test valid field formats: `name:string`, `email:string:unique:nullable`
   - Test invalid formats: `123:string`, `name:invalidtype`
   - Test modifier parsing: `:nullable`, `:unique`, combined
   - Implement regex-based parser

3. **ValidationRuleBuilder** (tests/Unit/ValidationRuleBuilderTest.php → src/Parsers/ValidationRuleBuilder.php)
   - Test type → rule mapping (string → `required|string|max:255`)
   - Test modifier application (`:nullable` → replace `required` with `nullable`)
   - Test unique rule generation (`:unique` → `unique:table,column`)
   - Implement rule builder logic

### Phase 2: Stub System (1 hour)

**Test → Implement → Validate**

4. **StubRenderer** (tests/Unit/StubRendererTest.php → src/Support/StubRenderer.php)
   - Test token replacement: `{{ RESOURCE_NAME }}` → `Product`
   - Test multiple token replacement
   - Test missing token detection
   - Implement string replacement logic

5. **Create Stub Templates** (resources/stubs/*.stub)
   - Copy from `example-files/` and convert to stubs
   - Add `{{ TOKEN }}` placeholders
   - Validate syntax (run through `php -l` for PHP stubs)

### Phase 3: Generators (2 hours)

**Test → Implement → Validate**

6. **ModelGenerator** (tests/Feature/ModelGeneratorTest.php → src/Generators/ModelGenerator.php)
   - Test basic model generation
   - Test with `--no-soft-deletes`
   - Test with `--no-auditing`
   - Test fillable fields generation
   - Test casts generation

7. **ControllerGenerator** (tests/Feature/ControllerGeneratorTest.php → src/Generators/ControllerGenerator.php)
   - Test controller structure (7 methods)
   - Test QueryBuilder integration
   - Test Inertia responses
   - Test Form Request usage

8. **RequestGenerator** (tests/Feature/RequestGeneratorTest.php → src/Generators/RequestGenerator.php)
   - Test StoreRequest generation
   - Test UpdateRequest generation (with unique ignore)
   - Test validation rules from field definitions

9. **ResourceGenerator** (tests/Feature/ResourceGeneratorTest.php → src/Generators/ResourceGenerator.php)
   - Test resource array structure
   - Test all fields included
   - Test timestamp formatting

10. **VueGenerator** (tests/Feature/VueGeneratorTest.php → src/Generators/VueGenerator.php)
    - Test Index.vue generation
    - Test Create.vue generation
    - Test Edit.vue generation
    - Test Form.vue component generation
    - Test TypeScript interfaces

### Phase 4: Atomic Generation (1 hour)

**Test → Implement → Validate**

11. **FileRollback** (tests/Unit/FileRollbackTest.php → src/Support/FileRollback.php)
    - Test file tracking
    - Test rollback on exception
    - Test commit clears tracking
    - Implement rollback manager

12. **Atomic Generation Test** (tests/Feature/AtomicRollbackTest.php)
    - Test partial failure triggers rollback
    - Test all files deleted on rollback
    - Test success commits all files

### Phase 5: Main Command (2 hours)

**Test → Implement → Validate**

13. **MakeCrudCommand** (tests/Feature/MakeCrudCommandTest.php → src/Commands/MakeCrudCommand.php)
    - Test argument parsing
    - Test option handling
    - Test output levels (normal, quiet, verbose)
    - Test complete generation flow
    - Test error handling
    - Implement command orchestration

### Phase 6: Service Provider (30 minutes)

14. **CrudGeneratorServiceProvider** (src/CrudGeneratorServiceProvider.php)
    - Register command
    - Publish configuration
    - Publish stubs
    - Test provider registration

### Phase 7: Quality Assurance (1 hour)

15. **Architecture Tests** (tests/Arch/ArchTest.php)
    - Test no debugging functions in generated code
    - Test PHPStan on generated code
    - Test Pint on generated code

16. **Integration Tests** (tests/Feature/CrudGenerationTest.php)
    - Test complete CRUD generation scenarios from spec
    - Test all acceptance scenarios from spec
    - Test edge cases from spec

## Running Tests

```bash
# Run all tests
composer test

# Run specific test suite
vendor/bin/pest tests/Unit
vendor/bin/pest tests/Feature
vendor/bin/pest tests/Arch

# Run with coverage
composer test-coverage

# Run PHPStan
composer analyse

# Run Pint (auto-fix)
composer format
```

## Validation Checklist

Before considering implementation complete:

- [ ] All tests pass (`composer test`)
- [ ] PHPStan Level 5 passes (`composer analyse`)
- [ ] Laravel Pint passes (`composer format`)
- [ ] All 19 functional requirements from spec.md implemented
- [ ] All 7 acceptance scenarios from User Story 1 tested
- [ ] All 4 acceptance scenarios from User Story 2 tested
- [ ] All edge cases from spec.md handled
- [ ] Constitution gates verified (re-run Constitution Check from plan.md)
- [ ] Generated code passes PHPStan Level 5 (SC-002)
- [ ] Generated code passes Laravel Pint (SC-002)
- [ ] CRUD generation completes in <30 seconds (SC-001)
- [ ] 90%+ use cases work without manual edits (SC-003)

## Common Pitfalls to Avoid

1. ❌ **Don't skip tests** - Constitution Principle III is non-negotiable
2. ❌ **Don't hardcode paths** - Use config file for flexibility
3. ❌ **Don't forget rollback** - Atomic generation is FR-017 requirement
4. ❌ **Don't ignore validation** - Early validation prevents bad generation
5. ❌ **Don't forget TypeScript** - Constitution Principle IV requires strict mode
6. ❌ **Don't copy-paste stubs** - Extract common logic, use proper token replacement
7. ❌ **Don't forget edge cases** - Reserved words, invalid types, disk full scenarios

## Next Steps After Implementation

1. Create `README.md` with installation and usage instructions
2. Create `CHANGELOG.md` following Keep a Changelog format
3. Create GitHub repository and push code
4. Tag version 1.0.0 release
5. Submit to Packagist for Composer installation
6. Create documentation site (optional)

## Reference Implementation

The `example-files/` directory contains the reference CRUD implementation that stubs should replicate. Use it as the source of truth for:

- File structure and organization
- Code patterns and conventions
- Naming conventions
- Component usage (Shadcn-vue, Spatie packages)

## Getting Help

- Review [research.md](research.md) for technical decision rationale
- Review [data-model.md](data-model.md) for entity relationships
- Review [contracts/](contracts/) for expected interfaces
- Check Spatie Laravel Package Tools docs: https://github.com/spatie/laravel-package-tools
- Check Orchestra Testbench docs: https://github.com/orchestral/testbench

## Time Estimates

| Phase | Estimated Time | Why |
|-------|---------------|-----|
| Setup | 15 min | Composer + directory structure |
| Core Utilities | 1 hour | Test → implement → validate 3 classes |
| Stub System | 1 hour | Renderer + template creation |
| Generators | 2 hours | 5 generators, test-first approach |
| Atomic Generation | 1 hour | Rollback manager + integration tests |
| Main Command | 2 hours | Command orchestration + error handling |
| Service Provider | 30 min | Simple registration and publishing |
| Quality Assurance | 1 hour | Arch tests + integration scenarios |
| **Total** | **8-10 hours** | First implementation with learning curve |

## Success Criteria Tracking

Map your implementation progress against Success Criteria from spec.md:

- **SC-001**: 30-second generation → Test with `time php artisan make:crud Product ...`
- **SC-002**: Code quality → Run PHPStan + Pint on generated files
- **SC-003**: 90% no edits → Manual testing with diverse field combinations
- **SC-004**: 80% time reduction → Compare manual CRUD creation vs command
- **SC-005**: 100% pattern match → Diff generated vs `example-files/`
- **SC-006**: 100% error messages → Test all invalid inputs, verify messages
- **SC-007**: Stub customization → Test vendor:publish + custom stub usage
- **SC-008**: Output levels → Test --quiet, normal, --verbose modes

Ready to start? Begin with Phase 1: Core Utilities using Test-First Development.
