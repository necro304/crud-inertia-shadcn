# Feature Specification: Package Rename to necro304/crud-inertia-shadcn

**Feature Branch**: `002-package-rename`
**Created**: 2025-11-15
**Status**: Draft
**Input**: User description: "Renombrar este paquete a necro304/crud-inertia-shadcn"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Package Installation with New Name (Priority: P1)

Developers installing the package for the first time should use the new package name `necro304/crud-inertia-shadcn` instead of the old placeholder name.

**Why this priority**: This is the most critical change as it establishes the new package identity. Without this, the package cannot be properly identified, discovered, or installed by users.

**Independent Test**: Can be fully tested by running `composer require necro304/crud-inertia-shadcn` in a fresh Laravel project and verifying successful installation with correct namespace resolution.

**Acceptance Scenarios**:

1. **Given** a fresh Laravel project, **When** developer runs `composer require necro304/crud-inertia-shadcn`, **Then** the package installs successfully with proper autoloading
2. **Given** an existing project with old package name, **When** developer updates composer.json with new package name and runs `composer update`, **Then** the package updates seamlessly without breaking existing functionality
3. **Given** the package is installed, **When** developer runs `php artisan`, **Then** all package commands appear with correct namespace and descriptions

---

### User Story 2 - Updated Package Documentation (Priority: P2)

Package documentation and README files should reflect the new package name, author, and repository information so developers can find correct installation and usage instructions.

**Why this priority**: After package identity is established, documentation is critical for onboarding new users and maintaining existing ones. Without updated docs, users will encounter confusion and incorrect instructions.

**Independent Test**: Can be fully tested by reviewing all documentation files (README.md, CLAUDE.md, composer.json) and verifying all references use the new package name and author information.

**Acceptance Scenarios**:

1. **Given** the README.md file, **When** developer reads installation instructions, **Then** they see `composer require necro304/crud-inertia-shadcn` as the correct command
2. **Given** the composer.json file, **When** developer views package metadata, **Then** package name shows as `necro304/crud-inertia-shadcn` and author shows as `necro304`
3. **Given** the package repository, **When** developer searches for package information, **Then** all links point to the correct GitHub repository under necro304 account

---

### User Story 3 - Configuration Script Updates (Priority: P3)

The configure.php script should use the new package name as the default or example, helping new users understand the expected package naming format.

**Why this priority**: While important for user experience, this is less critical than core identity and documentation. The configure script is a one-time setup tool that can still function with outdated examples.

**Independent Test**: Can be fully tested by running `php configure.php` and verifying that prompts, defaults, and examples reference the new package name.

**Acceptance Scenarios**:

1. **Given** a developer runs the configure script, **When** prompted for package name, **Then** the default or example shows `necro304/crud-inertia-shadcn`
2. **Given** the configure script completes, **When** developer reviews replaced values, **Then** all placeholders are replaced with consistent necro304/crud-inertia-shadcn references

---

### Edge Cases

- What happens when existing installations have the old package name in their composer.lock file?
- How does the system handle migration from old to new package name in active projects?
- What happens to existing GitHub issues, PRs, or references using the old package name?
- How are namespace references in generated code files handled during the rename?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST update composer.json with package name `necro304/crud-inertia-shadcn`
- **FR-002**: System MUST update composer.json with author information reflecting `necro304` as the package owner
- **FR-003**: System MUST update all README.md and documentation files to reference the new package name in installation instructions
- **FR-004**: System MUST update the configure.php script to use the new package name in defaults and examples
- **FR-005**: System MUST update namespace declarations and autoload paths to match the new package structure
- **FR-006**: System MUST update all references in CLAUDE.md files (both root and example) to reflect the new package name
- **FR-007**: System MUST preserve existing functionality and configuration structure during the rename
- **FR-008**: System MUST update package description and keywords in composer.json to accurately represent the package purpose
- **FR-009**: System MUST update any GitHub repository URLs, issue trackers, and source URLs to point to necro304/crud-inertia-shadcn
- **FR-010**: System MUST ensure PSR-4 autoloading continues to work correctly after namespace changes

### Key Entities

- **Package Metadata**: Composer.json configuration including name, author, description, namespace, autoload paths, and repository URLs
- **Documentation Files**: README.md, CLAUDE.md files containing installation instructions, usage examples, and package references
- **Configuration Script**: configure.php interactive tool that replaces package placeholders with actual values
- **Namespace References**: PHP namespace declarations in source code, service providers, and configuration files

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Package can be successfully installed in a fresh Laravel project using `composer require necro304/crud-inertia-shadcn` in under 60 seconds
- **SC-002**: All documentation files contain zero references to old placeholder package names when searched using grep or similar tools
- **SC-003**: Package autoloading works correctly with 100% of classes loading without namespace errors after installation
- **SC-004**: Configure.php script completes successfully with new package name defaults in under 2 minutes of user interaction time
- **SC-005**: All artisan commands registered by the package appear correctly in `php artisan list` output after installation
- **SC-006**: Existing package tests pass with 100% success rate after rename changes are applied

## Assumptions

- The package repository will be migrated to or created under the necro304 GitHub account
- The package name `necro304/crud-inertia-shadcn` is available on Packagist (or will be registered)
- Existing namespace structure follows PSR-4 conventions and can be updated without breaking external integrations
- The configure.php script is designed to handle package name replacements and won't require major refactoring
- No external packages or services have hard-coded dependencies on the old package name

## Out of Scope

- Migration scripts for existing installations (users will manually update their composer.json)
- Automated Packagist registration or GitHub repository creation
- Updating external documentation or blog posts referencing the old package name
- Creating package aliases or redirects from old to new package name
- Updating historical git commit messages or tags
