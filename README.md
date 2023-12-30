# Blade Icons Upgrader

This package is meant to be used to automatically search and replace old icons with new ones in your blade files when
using the [Blade Icons](https://github.com/blade-ui-kit/blade-icons) package.

It was initially made specifically for the [blade-heroicons]() package, but it can be used for any other icon package if
you
have the appropriate mapping file.

## Installation

You can install the package via composer:

```bash
composer require --dev blade-ui-kit/blade-icons-upgrader
```

## Usage

The package comes with a command that you can run to automatically search and replace old icons with new ones in your

```bash
php artisan blade-icons-upgrader run path/to/your/views
```

## Available mappings

| Package                                                            | Mapping file            |
|--------------------------------------------------------------------|-------------------------|
| [blade-heroicons](https://github.com/blade-ui-kit/blade-heroicons) | [blade-heroicons.json]( 
