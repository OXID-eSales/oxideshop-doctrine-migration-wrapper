# OXID eShop Doctrine Migration Wrapper

[![Actions Status](https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/workflows/Build/badge.svg)](https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/actions)

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
composer require oxid-esales/oxideshop-doctrine-migration-wrapper```
```

## Development

### Running tests

Component tests can be executed with the OXID eShop's PHPUnit runner:
```bash
vendor/bin/phpunit vendor/oxid-esales/oxideshop-doctrine-migration-wrapper
```

you might need to extend the eShop's root composer `autoload-dev` configuration and run `dump-autoload` command:

```json filename="composer.json"
    "autoload-dev": {
        "psr-4": {
            "OxidEsales\\DoctrineMigrationWrapper\\Tests\\": "./vendor/oxid-esales/oxideshop-doctrine-migration-wrapper/tests"
        }
    }
```

```bash
composer dump-autoload
```
to activate autoloading for the component's test classes.

## Bugs and Issues

If you experience any bugs or issues, please report them in the section **OXID eShop (all versions)** of 
https://bugs.oxid-esales.com.
