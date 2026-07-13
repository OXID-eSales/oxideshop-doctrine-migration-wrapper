# OXID eShop Doctrine Migration Wrapper

[![Actions Status](https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/workflows/Build/badge.svg)](https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/actions)

> **Deprecated:** This package is deprecated and will be removed in a future OXID eShop release.
> Use the OXID eShop console command to run database migrations instead:
>
> ```bash
> vendor/bin/oe-console oe:database:migrate
> ```

## Description

OXID eShop Doctrine Migration Wrapper was created to orchestrate multiple Doctrine Migrations, scattered all over the
OXID eShop project:

- migrations in OXID eShop Community Edition
- migrations in OXID eShop Professional Edition
- migrations in OXID eShop Enterprise Edition
- migrations in OXID eShop modules

## Installation

Run the following command to install:

```bash
composer require oxid-esales/oxideshop-doctrine-migration-wrapper
```

## Development

### Running tests

Component tests can be executed with the OXID eShop's PHPUnit runner:

```bash
vendor/bin/phpunit vendor/oxid-esales/oxideshop-doctrine-migration-wrapper
```

You might need to extend the eShop's root Composer `autoload-dev` configuration and run `dump-autoload`:

```json
{
    "autoload-dev": {
        "psr-4": {
            "OxidEsales\\DoctrineMigrationWrapper\\Tests\\": "./vendor/oxid-esales/oxideshop-doctrine-migration-wrapper/tests"
        }
    }
}
```

```bash
composer dump-autoload
```

This activates autoloading for the component's test classes.

## Bugs and Issues

If you experience any bugs or issues, report them in the **OXID eShop (all versions)** section of
https://bugs.oxid-esales.com.
