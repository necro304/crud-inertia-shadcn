# Task Completion Checklist

When a task is completed, run these commands in order:

## 1. Format Code
```bash
composer format
```
Runs Laravel Pint to ensure code style compliance.

## 2. Static Analysis
```bash
composer analyse
```
Runs PHPStan level 5 analysis on src/, config/, and database/.

## 3. Run Tests
```bash
composer test
```
Runs all Pest tests (Unit, Feature, Architecture).

## 4. Optional: Test Coverage
```bash
composer test-coverage
```
Generates test coverage report (use when needed).

## Quality Gates
- All tests must pass ✅
- No PHPStan errors ✅
- Code formatted according to Pint rules ✅
- Architecture tests pass (no dd/dump/ray functions) ✅

## Notes
- PHPStan baseline is tracked in phpstan-baseline.neon
- Octane compatibility is enforced
- Model properties are validated
- Random execution order for tests