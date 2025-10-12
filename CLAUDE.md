# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package that automates the upgrade process from Heroicons v1 to v2 for projects using the Blade Heroicons package. It replaces old icon names with their v2 equivalents in Blade templates.

## Development Commands

```bash
# Install dependencies
composer install

# Format code (Laravel Pint)
composer format

# Run static analysis (PHPStan)
composer analyse

# Run tests (Pest) - Note: No tests exist yet
composer test

# Run tests with coverage
composer test-coverage

# Build the package
composer build

# Start development server
composer start
```

## Architecture

The package consists of:

1. **Service Provider** (`src/BladeHeroiconsUpgraderServiceProvider.php`): Registers the package and publishes configuration
2. **Artisan Command** (`src/Commands/UpgradeIcons.php`): Main command that handles the upgrade process
    - Supports dry-run mode with `--dry-run` option
    - Can target specific files with `--file` option
    - Processes all Blade files in resources/views by default
3. **Configuration** (`config/blade-heroicons-upgrader.php`): Contains 235 icon name mappings from v1 to v2

## Key Implementation Details

The upgrade command uses regex patterns to replace icon names in two formats:

- `@svg()` directive: `@svg("old-icon")` → `@svg("new-icon")`
- Blade components: `<x-old-icon />` → `<x-new-icon />`

The regex patterns use custom word boundaries to handle:

- Icon name variants (outline 'o', solid 's', mini 'm')
- Special characters in icon names (dashes, slashes)
- Preventing partial matches within larger icon names

## Usage

```bash
# Preview changes without modifying files
php artisan blade-heroicons-upgrader:upgrade --dry-run

# Upgrade all Blade files in resources/views (default)
php artisan blade-heroicons-upgrader:upgrade

# Upgrade a specific file
php artisan blade-heroicons-upgrader:upgrade resources/views/welcome.blade.php

# Upgrade multiple paths
php artisan blade-heroicons-upgrader:upgrade ./resources/views ./app/Composers

# Dry run to preview changes
php artisan blade-heroicons-upgrader:upgrade --dry
```

## Testing

The package has comprehensive test coverage using Pest testing framework with PHPStan static analysis:

```bash
# Run tests
composer test

# Run tests with coverage
composer test-coverage

# Run static analysis
composer analyse

# Format code
composer format
```

### Test Coverage

- **Unit tests** (`tests/Unit/`): Test the `IconReplacer` class with various icon formats
- **Feature tests** (`tests/Feature/`): Test the full command with real-world sample files
- **Architecture tests** (`tests/ArchTest.php`): Ensure no debugging functions are used
- **Test datasets** (`tests/Datasets/`): Comprehensive test cases for different icon syntax
- **Sample files** (`tests/Samples/`): Real-world Blade templates for integration testing

Focus areas:

- Regex pattern matching for various icon formats (@svg, blade components, quoted strings)
- Edge cases in icon name replacements (overlapping names, partial matches)
- Dry-run vs actual file modification behavior
- File discovery and processing logic
