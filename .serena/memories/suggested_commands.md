# Suggested Commands

## Testing
```bash
# Run all tests
composer test
# or
vendor/bin/pest

# Run tests with coverage
composer test-coverage
# or
vendor/bin/pest --coverage

# Run specific test file
vendor/bin/pest tests/Unit/ValidationRuleBuilderTest.php

# Run tests with filter
vendor/bin/pest --filter=test_name
```

## Code Quality
```bash
# Run static analysis (PHPStan level 5)
composer analyse
# or
vendor/bin/phpstan analyse

# Format code (Laravel Pint)
composer format
# or
vendor/bin/pint
```

## Package Discovery
```bash
# Discover packages (runs automatically after composer install)
composer run prepare
# or
php vendor/bin/testbench package:discover --ansi
```

## Installation
```bash
# Install dependencies
composer install

# Update dependencies
composer update
```

## System Utilities (Darwin/macOS)
```bash
# File operations
ls          # List files
cd          # Change directory
find        # Find files
grep        # Search in files

# Git operations
git status
git add .
git commit -m "message"
git push
```