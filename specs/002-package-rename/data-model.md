# Data Model: Package Rename to necro304/crud-inertia-shadcn

**Phase**: 1 - Design & Contracts
**Date**: 2025-11-15
**Branch**: 002-package-rename

## Overview

This feature is a metadata rename operation with NO database entities, API endpoints, or data persistence layer. The "data" in this context refers to configuration files and text-based metadata that define the package identity.

## Configuration Entities

### 1. Package Metadata (composer.json)

**Purpose**: Composer package configuration defining package identity, dependencies, and autoloading.

**Fields**:
| Field | Type | Constraints | Purpose |
|-------|------|-------------|---------|
| name | string | Format: `vendor/package`, lowercase, required | Unique package identifier for Composer |
| description | string | Max 255 chars, required | Human-readable package purpose |
| keywords | array<string> | Relevant search terms | Packagist discoverability |
| homepage | URL | Valid GitHub URL | Repository homepage |
| license | string | SPDX identifier, required | Package license (e.g., MIT) |
| authors | array<object> | Min 1 author | Package maintainers |
| authors[].name | string | Required | Author full name |
| authors[].email | email | Valid email format | Contact email |
| require | object | Version constraints | Runtime dependencies |
| require-dev | object | Version constraints | Development dependencies |
| autoload.psr-4 | object | Namespace→path mapping | PSR-4 autoloading config |
| autoload-dev.psr-4 | object | Namespace→path mapping | Test namespace autoloading |
| extra.laravel.providers | array<string> | Fully qualified class names | Laravel service provider auto-discovery |
| support.issues | URL | Valid GitHub issues URL | Issue tracker link |
| support.source | URL | Valid GitHub source URL | Source repository link |

**Validation Rules**:
- Package name MUST follow `^[a-z0-9]([_.-]?[a-z0-9]+)*/[a-z0-9](([_.]?|-{0,2})[a-z0-9]+)*$` regex
- PSR-4 namespaces MUST end with `\\`
- All URLs MUST use HTTPS protocol
- License MUST be valid SPDX identifier
- PHP version constraint MUST be `^8.4` (or `^8.3|^8.4`)

**Current State → Target State**:
```
name: ":vendor_slug/:package_slug" → "necro304/crud-inertia-shadcn"
homepage: "" → "https://github.com/necro304/crud-inertia-shadcn"
authors[0].name: ":author_name" → "necro304"
autoload.psr-4: {"Vendor\\Package\\": "src/"} → {"Necro304\\CrudInertiaShadcn\\": "src/"}
```

---

### 2. Documentation Metadata (README.md, CLAUDE.md)

**Purpose**: User-facing installation guide and AI agent development context.

**README.md Fields**:
| Section | Content | Validation |
|---------|---------|------------|
| Title | `# PackageName` | H1 heading, matches package description |
| Installation | `composer require vendor/package` | Valid composer command |
| Description | Package purpose and features | Clear, concise, accurate |
| Usage Examples | Code samples with correct namespace | Syntax-highlighted, runnable |
| Testing | Test command examples | Executable commands |
| Repository Links | Badges and URLs | Valid URLs, correct package name |

**CLAUDE.md Fields**:
| Section | Content | Validation |
|---------|---------|------------|
| Project Overview | Package name and purpose | Matches composer.json description |
| Package Configuration | Reference to configure.php | Accurate setup instructions |
| Active Technologies | Tech stack list | Current versions listed |
| Recent Changes | Change log entry | Documents package rename |

**Validation Rules**:
- Installation command MUST match composer.json `name` field
- Repository URLs MUST match composer.json `support` URLs
- Code examples MUST use correct PSR-4 namespace
- Version badges MUST reference correct Packagist package

---

### 3. Configuration Script State (configure.php)

**Purpose**: Interactive script for replacing package skeleton placeholders.

**Placeholder Variables**:
| Placeholder | Target Value | Used In |
|-------------|--------------|---------|
| `:vendor_name` | `Necro304` | Namespace declarations |
| `:vendor_slug` | `necro304` | composer.json name, URLs |
| `:package_name` | `Crud Inertia Shadcn` | Human-readable name |
| `:package_slug` | `crud-inertia-shadcn` | composer.json name, URLs |
| `:package_description` | Package description text | composer.json, README |
| `:author_name` | Author name | composer.json authors |
| `:author_email` | Author email | composer.json authors |
| `:author_username` | `necro304` | GitHub URLs, package paths |

**Default Values** (updated for this rename):
```php
$vendorName = 'Necro304';
$vendorSlug = 'necro304';
$packageName = 'Crud Inertia Shadcn';
$packageSlug = 'crud-inertia-shadcn';
$packageDescription = 'Laravel package for generating CRUD with Inertia.js and Shadcn UI';
```

**Validation Rules**:
- Vendor slug MUST be lowercase, alphanumeric + hyphens
- Package slug MUST be lowercase, alphanumeric + hyphens
- Namespace (vendor name) MUST be PascalCase
- Email MUST pass PHP `filter_var(FILTER_VALIDATE_EMAIL)`

**State Tracking**:
- Script is idempotent (safe to run multiple times)
- Detects already-configured packages (no placeholders found)
- Preserves manual edits between runs

---

## Namespace Mappings

### PSR-4 Autoload Mapping

**Current** (to be verified):
```
Vendor\Package\ → src/
Vendor\Package\Tests\ → tests/
```

**Target**:
```
Necro304\CrudInertiaShadcn\ → src/
Necro304\CrudInertiaShadcn\Tests\ → tests/
```

**Affected Files** (namespace declarations):
- `src/CrudGeneratorServiceProvider.php`
- `src/Commands/*.php`
- `src/Facades/*.php`
- `src/Support/*.php`
- `tests/TestCase.php`
- `tests/Pest.php`
- `tests/*Test.php`

**Migration Process**:
1. Update composer.json autoload sections
2. Find/replace namespace declarations in PHP files
3. Run `composer dump-autoload`
4. Verify with `composer validate --strict`
5. Run PHPStan to catch namespace resolution errors

---

## File-Based State

### Repository URLs

**GitHub Repository URL**: `https://github.com/necro304/crud-inertia-shadcn`

**URL Usage**:
- composer.json `homepage`
- composer.json `support.issues`: `{repo}/issues`
- composer.json `support.source`: `{repo}`
- README.md repository badges
- README.md documentation links
- CLAUDE.md repository references

**Validation**: All URLs MUST return HTTP 200 after repository creation.

---

### Version Identifiers

**Packagist Version**: Determined by Git tags (e.g., `v1.0.0`, `v1.1.0`)

**Version Sources**:
- Git tags: Semantic versioning (MAJOR.MINOR.PATCH)
- composer.json: No hardcoded version (detected from VCS)
- README.md badges: Auto-updated by Packagist

**Release Process**:
1. Merge rename changes to main branch
2. Create Git tag: `git tag v1.0.0`
3. Push tag: `git push origin v1.0.0`
4. Packagist auto-detects version from tag

---

## Validation Rules Summary

### Composer Validation
```bash
composer validate --strict
```
**Must Pass**:
- Package name format compliance
- PSR-4 autoload paths exist
- All required fields present
- No deprecated fields
- License is valid SPDX identifier

### PSR-4 Autoload Verification
```php
// All classes must autoload without errors
use Necro304\CrudInertiaShadcn\CrudGeneratorServiceProvider;
use Necro304\CrudInertiaShadcn\Commands\GenerateCrudCommand;
// etc.
```

### PHPStan Static Analysis
```bash
vendor/bin/phpstan analyse --level=5 src config database
```
**Must Pass**:
- Zero namespace resolution errors
- Zero undefined class errors
- Zero type inference errors

### Laravel Service Provider Discovery
```bash
php artisan list | grep crud
```
**Must Show**: All package-registered artisan commands

---

## State Transitions

### Package Identity State Machine

```
State: SKELETON_PLACEHOLDER
  Fields: name=":vendor_slug/:package_slug"
  Actions: configure.php interactive setup
  ↓
State: CONFIGURED_LOCAL
  Fields: name="necro304/crud-inertia-shadcn"
  Actions: Git commit, push to GitHub
  ↓
State: PUBLISHED_GITHUB
  Fields: GitHub repo exists at necro304/crud-inertia-shadcn
  Actions: Register on Packagist
  ↓
State: REGISTERED_PACKAGIST
  Fields: Discoverable via `composer require necro304/crud-inertia-shadcn`
  Actions: Create Git tag for version
  ↓
State: VERSIONED_RELEASE
  Fields: Installable with version constraint
  Actions: Normal package maintenance
```

**Current Implementation Target**: Transition from SKELETON_PLACEHOLDER → CONFIGURED_LOCAL

**Out of Scope** (for this feature):
- GitHub repository creation (manual)
- Packagist registration (manual)
- Git tag creation (future release)

---

## No Database Schema

This feature has ZERO database impact:
- No migrations required
- No model changes
- No database queries
- No schema modifications
- No seeders needed

**Rationale**: This is a package metadata rename, not a data model feature.

---

## Summary

This feature manages three types of "data":

1. **Configuration Data** (composer.json): Package identity metadata
2. **Documentation Data** (README, CLAUDE.md): Human-readable package information
3. **Script Data** (configure.php): Placeholder→value mappings

All data is file-based with text validation rules. No persistent storage or API contracts required.
