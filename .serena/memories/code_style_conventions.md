# Code Style and Conventions

## Code Style Tool
Laravel Pint with custom configuration (pint.json)

## Style Rules
- **Preset**: Laravel
- **Imports**: Alphabetically ordered (`ordered_imports`)
- **Quotes**: Single quotes (`single_quote`)
- **Concatenation**: One space around concat operator (`concat_space`)
- **Method Chaining**: Proper indentation (`method_chaining_indentation`)
- **PHPDoc**: Left-aligned, properly separated
- **Trailing Commas**: In multiline arrays only
- **Not Operator**: Space after (`not_operator_with_successor_space`)

## Naming Conventions
- **PSR-4 Autoloading**: `Isaac\CrudGenerator\` namespace for src
- **Test Namespace**: `Isaac\CrudGenerator\Tests\` for tests
- **Service Provider**: CrudGeneratorServiceProvider
- **Commands**: Artisan commands in `src/Commands/`
- **Generators**: Code generators in `src/Generators/`

## File Organization
- `src/` - Package source code
  - `Commands/` - Artisan commands
  - `Generators/` - Code generators
  - `Parsers/` - Field and validation parsers
  - `Support/` - Support classes and utilities
  - `Facades/` - Package facades
- `tests/` - Test files
  - `Unit/` - Unit tests
  - `Feature/` - Feature tests
  - `Arch/` - Architecture tests
- `config/` - Configuration files
- `database/` - Migrations
- `resources/` - Views and assets

## Testing Structure
- Uses Pest PHP 4.0
- Test suites: Unit, Feature, Architecture
- Architecture tests to prevent debug functions (dd, dump, ray)
- TestCase extends Orchestra Testbench

## Static Analysis
- PHPStan level 5
- Octane compatibility checks
- Model properties validation
- Paths analyzed: src, config, database