# Changelog

All notable changes to `blade-heroicons-upgrader` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.2] - 2025-10-12

### Added

- PHPStan static analysis configuration (`phpstan.neon.dist`)
- Automated installation test script (`test-install.sh`) for testing package installation across Laravel 10, 11, and 12
- New `composer test-install` command for running installation tests
- New `composer e2e` command for running full end-to-end test suite (format, analyse, test, test-coverage, test-install)

### Changed

- Improved test coverage to 100% by adding tests for interactive prompt behavior
- Configured PHPStan to analyze only `src/` directory, excluding `tests/` to avoid Pest compatibility issues
- Updated `.gitignore` to exclude `.claude` directory

### Fixed

- Fixed PHPStan type error in `UpgradeIcons` command by ensuring file paths are consistently typed as strings

## [1.1.0] - 2025-10-11

### Fixed

- Corrected 9 icon mappings to match official Heroicons v1 to v2 migration guide:
    - `arrow-sm-down` now correctly maps to `arrow-small-down` (was `arrow-down`)
    - `arrow-sm-up` now correctly maps to `arrow-small-up` (was `arrow-up`)
    - `arrow-sm-left` now correctly maps to `arrow-small-left` (was `arrow-left`)
    - `arrow-sm-right` now correctly maps to `arrow-small-right` (was `arrow-right`)
    - `plus-sm` now correctly maps to `plus-small` (was `plus`)
    - `minus-sm` now correctly maps to `minus-small` (was `minus`)
    - `pencil-alt` now correctly maps to `pencil-square` (was `pencil`)
    - `database` now correctly maps to `circle-stack` (was `server`)
    - `upload` now correctly maps to `arrow-up-tray` (was `cloud-arrow-up`)

### Added

- Laravel 12 support
- Support for Laravel Prompts v0.2 and v0.3
- Comprehensive test coverage with Pest
- GitHub Actions workflow for automated testing

### Changed

- Updated `laravel/prompts` constraint to support multiple versions: `^0.1.14|^0.2|^0.3`
- Updated `orchestra/testbench` to support Laravel 10, 11, and 12: `^8.8|^9.0|^10.0`
- Updated `nunomaduro/collision` to support Laravel 10, 11, and 12: `^7.8|^8.0`
- Updated `pestphp/pest` to latest versions: `^2.30|^3.0|^4.0`
- Updated `pestphp/pest-plugin-laravel` to latest versions: `^2.0|^3.0|^4.0`
- Updated `pestphp/pest-plugin-arch` to latest versions: `^2.0|^3.0`
- Improved documentation with Laravel version requirements
- Fixed command usage documentation to reflect actual argument-based syntax
- Package description in composer.json (was placeholder text)

## [1.0.0] - 2024-01-02

### Added

- Initial release
- Support for upgrading Heroicons v1 to v2 in Blade templates
- Support for `@svg()` directive syntax
- Support for Blade component syntax (`<x-icon-name />`)
- Support for icons in quoted strings
- Dry-run mode with `--dry` flag
- Interactive Laravel Prompts UI
- Prevents overlapping icon replacements
- 235 icon name mappings from v1 to v2
- Comprehensive regex-based replacement logic
- Support for Laravel 10 and 11
- PHP 8.1+ requirement

[Unreleased]: https://github.com/helgesverre/blade-heroicons-upgrader/compare/v1.2.2...HEAD
[1.2.2]: https://github.com/helgesverre/blade-heroicons-upgrader/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/helgesverre/blade-heroicons-upgrader/compare/v1.1.0...v1.2.1
[1.1.0]: https://github.com/helgesverre/blade-heroicons-upgrader/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/helgesverre/blade-heroicons-upgrader/releases/tag/v1.0.0
