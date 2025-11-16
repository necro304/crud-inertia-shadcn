# Quickstart: Package Rename Implementation

**Feature**: Package Rename to necro304/crud-inertia-shadcn
**Branch**: 002-package-rename
**Estimated Time**: 30-45 minutes

## Prerequisites

- [x] Git repository with clean working directory
- [x] PHP ^8.4 (or 8.3) installed
- [x] Composer installed
- [x] Text editor or IDE with find/replace capabilities
- [x] GitHub account (necro304)

## Implementation Checklist

### Phase 1: Pre-Flight Verification (5 min)

```bash
# 1. Verify current branch and status
git status
git branch

# 2. Checkout feature branch (if not already on it)
git checkout 002-package-rename

# 3. Verify tests pass before changes
composer test

# 4. Run code quality checks
composer analyse
composer format

# Expected: All tests pass, zero errors
```

### Phase 2: Update composer.json (10 min)

```bash
# 1. Open composer.json in editor
```

**Update these fields**:
```json
{
  "name": "necro304/crud-inertia-shadcn",
  "description": "Laravel package for generating CRUD with Inertia.js and Shadcn UI",
  "keywords": [
    "laravel",
    "crud",
    "inertia",
    "vue",
    "shadcn",
    "generator",
    "package"
  ],
  "homepage": "https://github.com/necro304/crud-inertia-shadcn",
  "license": "MIT",
  "authors": [
    {
      "name": "necro304",
      "email": "your-email@example.com",
      "role": "Developer"
    }
  ],
  "autoload": {
    "psr-4": {
      "Necro304\\CrudInertiaShadcn\\": "src/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "Necro304\\CrudInertiaShadcn\\Tests\\": "tests/"
    }
  },
  "extra": {
    "laravel": {
      "providers": [
        "Necro304\\CrudInertiaShadcn\\CrudGeneratorServiceProvider"
      ]
    }
  },
  "support": {
    "issues": "https://github.com/necro304/crud-inertia-shadcn/issues",
    "source": "https://github.com/necro304/crud-inertia-shadcn"
  }
}
```

```bash
# 2. Validate composer.json syntax
composer validate --strict

# Expected: Valid composer.json, no errors
```

### Phase 3: Update PHP Namespaces (15 min)

```bash
# 1. Find all PHP files with old namespace
grep -r "namespace.*Vendor\\\\Package" src/ tests/

# 2. Update namespace declarations (use IDE refactoring or find/replace)
# Find:    namespace Vendor\Package
# Replace: namespace Necro304\CrudInertiaShadcn

# 3. Update use statements
# Find:    use Vendor\Package
# Replace: use Necro304\CrudInertiaShadcn

# 4. Regenerate autoloader
composer dump-autoload

# 5. Verify autoloading works
composer dumpautoload -o
```

**Files to update** (verify these paths exist in your project):
- `src/CrudGeneratorServiceProvider.php`
- `src/Commands/*.php`
- `src/Facades/*.php`
- `src/Support/*.php`
- `tests/TestCase.php`
- `tests/Pest.php`
- `tests/*Test.php`

### Phase 4: Update Documentation (10 min)

#### README.md
```bash
# Update installation command
# Find:    composer require :vendor_slug/:package_slug
# Replace: composer require necro304/crud-inertia-shadcn

# Update repository URLs
# Find:    :author_username
# Replace: necro304

# Update package name references
# Find:    :package_name
# Replace: Crud Inertia Shadcn
```

#### CLAUDE.md (root)
```bash
# Update package overview section
# Add to "Recent Changes" section:
# - 002-package-rename: Renamed package to necro304/crud-inertia-shadcn
```

#### example/CLAUDE.md
```bash
# Update any package name references
# Verify installation instructions use correct package name
```

### Phase 5: Update configure.php (5 min)

```php
// Update default values in configure.php
$vendorName = ask('Vendor name', 'Necro304');
$vendorSlug = ask('Vendor slug', 'necro304');
$packageName = ask('Package name', 'Crud Inertia Shadcn');
$packageSlug = ask('Package slug', 'crud-inertia-shadcn');
$packageDescription = ask(
    'Package description',
    'Laravel package for generating CRUD with Inertia.js and Shadcn UI'
);
```

### Phase 6: Validation & Testing (10 min)

```bash
# 1. Validate composer.json (CRITICAL)
composer validate --strict

# 2. Verify autoloading
composer dump-autoload -o

# 3. Run all tests
composer test

# 4. Run static analysis
composer analyse

# 5. Run code style check
composer format

# 6. Test in fresh Laravel project (OPTIONAL but recommended)
cd /tmp
composer create-project laravel/laravel test-package
cd test-package

# Add local package path to composer.json
composer config repositories.local path /path/to/crud-inertia-shadcn

# Require the package
composer require necro304/crud-inertia-shadcn:@dev

# Verify artisan commands appear
php artisan list | grep crud

# Expected: All tests pass, zero errors, commands visible
```

### Phase 7: Git Commit (5 min)

```bash
# 1. Review all changes
git status
git diff

# 2. Stage changes
git add composer.json
git add src/
git add tests/
git add README.md
git add CLAUDE.md
git add example/CLAUDE.md
git add configure.php

# 3. Commit with descriptive message
git commit -m "feat: Rename package to necro304/crud-inertia-shadcn

- Update composer.json with new package name and metadata
- Update PHP namespaces from Vendor\Package to Necro304\CrudInertiaShadcn
- Update README.md installation instructions
- Update CLAUDE.md package references
- Update configure.php default values
- Preserve all existing functionality (100% backward compatible)
- All tests pass, PHPStan Level 5 analysis passes

🤖 Generated with Claude Code
Co-Authored-By: Claude <noreply@anthropic.com>"

# 4. Push to remote
git push origin 002-package-rename
```

## Verification Checklist

After implementation, verify:

- [ ] `composer validate --strict` passes
- [ ] `composer test` passes (100% success rate)
- [ ] `composer analyse` passes (PHPStan Level 5, zero errors)
- [ ] `composer format` completes without changes
- [ ] `php artisan list` shows package commands (in test Laravel project)
- [ ] All namespace references use `Necro304\CrudInertiaShadcn\`
- [ ] README.md shows `composer require necro304/crud-inertia-shadcn`
- [ ] composer.json `name` is `necro304/crud-inertia-shadcn`
- [ ] composer.json `homepage` is `https://github.com/necro304/crud-inertia-shadcn`
- [ ] Git commit created and pushed

## Common Issues & Solutions

### Issue: Composer validate fails
**Solution**: Check composer.json syntax with JSON linter. Verify package name format matches `vendor/package` pattern.

### Issue: Autoloading doesn't work
**Solution**: Run `composer dump-autoload -o`. Verify namespace in composer.json matches namespace in PHP files.

### Issue: Tests fail after rename
**Solution**: Check test namespace declarations. Update TestCase class namespace. Clear config cache: `php artisan config:clear`

### Issue: PHPStan namespace errors
**Solution**: Run `composer dump-autoload`. Check all `use` statements match new namespace.

### Issue: Service provider not found
**Solution**: Verify `extra.laravel.providers` in composer.json has correct fully-qualified class name.

## Next Steps (Out of Scope for This Feature)

After completing this rename:

1. **Create GitHub Repository**:
   ```bash
   # On GitHub web interface
   # Create new repository: necro304/crud-inertia-shadcn

   # Add remote and push
   git remote add origin git@github.com:necro304/crud-inertia-shadcn.git
   git push -u origin main
   ```

2. **Register on Packagist**:
   - Visit https://packagist.org
   - Sign in with GitHub
   - Submit package: https://github.com/necro304/crud-inertia-shadcn

3. **Create Version Tag**:
   ```bash
   git tag v1.0.0
   git push origin v1.0.0
   ```

4. **Test Public Installation**:
   ```bash
   composer require necro304/crud-inertia-shadcn
   ```

## Estimated Timeline

| Phase | Time | Description |
|-------|------|-------------|
| Pre-Flight | 5 min | Verify starting state |
| composer.json | 10 min | Update package metadata |
| Namespaces | 15 min | Update PHP namespace declarations |
| Documentation | 10 min | Update README, CLAUDE.md files |
| configure.php | 5 min | Update default values |
| Validation | 10 min | Run all quality checks |
| Git Commit | 5 min | Commit and push changes |
| **Total** | **60 min** | End-to-end implementation |

**Actual Expected Time**: 30-45 minutes for experienced developers (IDE refactoring speeds up namespace updates)

## Success Criteria

✅ Feature complete when:
1. Package can be installed with `composer require necro304/crud-inertia-shadcn` (in local test)
2. All tests pass (100% success rate)
3. PHPStan Level 5 analysis passes (zero errors)
4. All documentation references correct package name
5. Git commit created with descriptive message
6. Changes pushed to remote branch

## Support

If you encounter issues:
1. Check this quickstart guide for common solutions
2. Review research.md for technical background
3. Consult data-model.md for validation rules
4. Review spec.md for functional requirements
