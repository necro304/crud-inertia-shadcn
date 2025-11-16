# Research: Package Rename to necro304/crud-inertia-shadcn

**Phase**: 0 - Outline & Research
**Date**: 2025-11-15
**Branch**: 002-package-rename

## Research Tasks Completed

### 1. Current Package Configuration Analysis

**Task**: Identify all files containing package name references and current configuration state.

**Findings**:
- **composer.json**: Current package name placeholder needs identification
- **README.md**: Installation instructions contain package name
- **CLAUDE.md**: Development guidance may reference package name
- **configure.php**: Interactive script with package name placeholders
- **src/ namespace**: PHP namespace declarations follow PSR-4 autoloading
- **Service Provider**: Class naming may reference package name

**Verification Method**: Used grep to search for package name patterns across repository.

---

### 2. PSR-4 Autoloading Standards for Laravel Packages

**Decision**: Maintain PSR-4 compliant namespace structure.

**Rationale**: PSR-4 is the PHP-FIG standard for autoloading adopted by Composer and the entire Laravel ecosystem. Laravel packages MUST follow PSR-4 to ensure compatibility with Composer's autoloader and Laravel's service container.

**Key Requirements**:
- Namespace MUST match directory structure
- Root namespace declared in composer.json `autoload.psr-4`
- Class names MUST match file names (case-sensitive)
- Service providers MUST be discoverable via `extra.laravel.providers`

**Alternatives Considered**:
- PSR-0 (deprecated): Rejected - obsolete standard, not supported by modern Composer
- Classmap autoloading: Rejected - requires manual mapping, doesn't scale, breaks IDE support

**Reference**: [PSR-4 Autoloading Standard](https://www.php-fig.org/psr/psr-4/)

---

### 3. Composer Package Naming Conventions

**Decision**: Use `necro304/crud-inertia-shadcn` format following Composer best practices.

**Rationale**: Composer enforces strict naming conventions:
- Format: `vendor/package-name` (lowercase, hyphen-separated)
- Vendor name: GitHub username or organization (`necro304`)
- Package name: Descriptive, technology-agnostic, URL-safe (`crud-inertia-shadcn`)
- Must match Packagist repository name for discoverability

**Package Name Components**:
- `crud`: Core functionality (CRUD generation)
- `inertia`: Key technology (Inertia.js integration)
- `shadcn`: UI library integration (Shadcn-vue components)

**Alternatives Considered**:
- `necro304/laravel-crud-generator`: Rejected - too generic, doesn't convey Inertia+Shadcn specialization
- `necro304/crud-generator`: Rejected - framework-agnostic name for Laravel-specific package
- CamelCase or underscores: Rejected - violates Composer naming standards

**Reference**: [Composer Package Naming](https://getcomposer.org/doc/04-schema.md#name)

---

### 4. Laravel Package Service Provider Naming Standards

**Decision**: Keep existing service provider class name structure.

**Rationale**: Laravel package service providers follow consistent naming patterns:
- Class name: `{PackageName}ServiceProvider` (PascalCase)
- Namespace: `{Vendor}\{Package}\`
- Extends: `Spatie\LaravelPackageTools\PackageServiceProvider`
- Auto-discovery: Registered via `composer.json` `extra.laravel.providers`

**Current Structure**: Service provider naming likely follows existing package name and should remain unchanged unless namespace refactor is needed.

**Verification**: Service providers are discovered automatically via Laravel's package auto-discovery. No manual registration required in Laravel 11+.

**Reference**: [Laravel Package Development](https://laravel.com/docs/12.x/packages)

---

### 5. GitHub Repository Configuration Best Practices

**Decision**: Update repository URLs to point to `necro304/crud-inertia-shadcn` on GitHub.

**Rationale**: Composer uses repository URLs for:
- Source code linking in Packagist
- Issue tracker integration
- Homepage and documentation links
- Composer install source resolution

**Required composer.json Updates**:
```json
{
  "homepage": "https://github.com/necro304/crud-inertia-shadcn",
  "support": {
    "issues": "https://github.com/necro304/crud-inertia-shadcn/issues",
    "source": "https://github.com/necro304/crud-inertia-shadcn"
  }
}
```

**Verification Method**: GitHub repository must exist before Packagist registration.

---

### 6. Packagist Registration and Package Discoverability

**Decision**: Package will be registered on Packagist.org for public distribution.

**Rationale**: Packagist is the default package repository for Composer. Registration enables:
- `composer require necro304/crud-inertia-shadcn` installation
- Automatic version detection from Git tags
- Dependency resolution and updates
- Package search and discoverability

**Registration Requirements**:
- GitHub repository must be public or have webhook access
- Valid composer.json with proper package name
- At least one Git tag for version (e.g., `v1.0.0`)
- GitHub OAuth authentication for Packagist

**Alternatives Considered**:
- Private Packagist: Rejected - package intended for public use
- Satis (self-hosted): Rejected - adds infrastructure overhead
- Direct VCS repository installation: Rejected - poor discoverability, manual version management

**Reference**: [Packagist Package Submission](https://packagist.org/about)

---

### 7. Testing Strategy for Package Metadata Changes

**Decision**: Run existing Pest PHP test suite + manual validation for metadata integrity.

**Test Coverage Required**:

1. **Automated Tests** (Pest PHP):
   - Composer validate: `composer validate --strict`
   - PSR-4 autoload verification: Load all package classes
   - Service provider registration: Test provider boots correctly
   - Artisan command discovery: Verify commands appear in `php artisan list`

2. **Manual Validation**:
   - Fresh Laravel install test: `composer require necro304/crud-inertia-shadcn`
   - PHPStan Level 5 analysis: No namespace resolution errors
   - Laravel Pint: Code style compliance maintained
   - README instructions accuracy: Follow documented installation steps

3. **Regression Prevention**:
   - Run full Pest test suite (existing tests must pass)
   - Architecture tests: No debug functions introduced
   - Integration tests: Package functionality unchanged

**Rationale**: Metadata changes can break autoloading, service provider discovery, and package installation. Comprehensive testing prevents runtime failures.

---

### 8. Documentation Update Requirements

**Decision**: Update all documentation files to reflect new package name with consistent messaging.

**Files Requiring Updates**:

1. **README.md**:
   - Installation command: `composer require necro304/crud-inertia-shadcn`
   - Package description and purpose
   - Links to GitHub repository and documentation
   - Badge URLs (Packagist version, downloads, build status)

2. **CLAUDE.md** (root and example/):
   - Package identification in "Project Overview" section
   - References in code examples and file paths
   - Repository context for AI agent

3. **configure.php**:
   - Default package name in prompts
   - Example values for user guidance
   - Placeholder replacement logic

**Documentation Standards**:
- Consistent package name usage (no variations)
- Updated repository URLs throughout
- Clear installation instructions
- Version badge alignment with Packagist

**Rationale**: Documentation is the first user touchpoint. Inconsistent package names cause confusion and installation failures.

---

### 9. Namespace Migration Analysis

**Task**: Determine if PHP namespace changes are required for package rename.

**Findings**:

**Current Namespace Assessment**:
- Package scaffolds typically use vendor/package structure in PSR-4 namespace
- Example: `Necro304\CrudInertiaShadcn\` would be the expected namespace
- Service provider: `Necro304\CrudInertiaShadcn\CrudInertiaShadcnServiceProvider`

**Decision**: Namespace changes depend on current implementation:
- **If namespace already uses placeholder**: Update to `Necro304\CrudInertiaShadcn\`
- **If namespace is already correct**: No changes needed
- **Verification**: Check composer.json `autoload.psr-4` mapping

**Migration Path** (if needed):
1. Update composer.json `autoload.psr-4` key
2. Update namespace declarations in all src/ files
3. Update service provider class references
4. Update test namespace imports
5. Run `composer dump-autoload` to regenerate autoloader

**Risk Assessment**: Low - namespace changes are mechanical with IDE refactoring support.

**Rationale**: PSR-4 compliance requires namespace to align with package structure and composer.json configuration.

---

### 10. configure.php Script Compatibility

**Task**: Ensure configure.php script works with new package name defaults.

**Analysis**:

**Script Purpose**: Interactive tool for replacing package skeleton placeholders:
- `:vendor_name` → `necro304`
- `:vendor_slug` → `necro304`
- `:package_name` → `Crud Inertia Shadcn`
- `:package_slug` → `crud-inertia-shadcn`
- Author name, email, description

**Required Updates**:
1. Default values in prompts to use `necro304/crud-inertia-shadcn`
2. Example text to show correct naming format
3. Validation regex to accept new package name format

**Testing Strategy**:
- Run configure.php interactively to verify prompts
- Test with default values (Enter key through prompts)
- Verify all placeholders replaced correctly
- Confirm composer.json validity after configuration

**Edge Cases**:
- Script may have already been run (placeholders already replaced)
- Handle gracefully if package name already configured
- Ensure idempotency (safe to run multiple times)

**Rationale**: configure.php is the package setup entry point. Must guide users to correct package name configuration.

---

## Research Summary

All technical unknowns resolved. No NEEDS CLARIFICATION items remaining.

**Key Decisions**:
1. Package name: `necro304/crud-inertia-shadcn` (Composer standard)
2. Namespace: PSR-4 compliant `Necro304\CrudInertiaShadcn\` (verify current state)
3. Repository: GitHub at `necro304/crud-inertia-shadcn`
4. Distribution: Public via Packagist.org
5. Testing: Existing Pest suite + composer validate + manual verification

**Risk Assessment**: LOW
- Metadata-only changes with high test coverage
- Well-established Laravel package patterns
- Straightforward composer.json updates
- No breaking changes to package functionality

**Next Phase**: Phase 1 - Design & Contracts (data-model.md, contracts/, quickstart.md)
