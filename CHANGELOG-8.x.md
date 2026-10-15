# Change Log for OXID eShop Doctrine Migration Wrapper

## v8.0.0-alpha.3 - Unreleased
*Compilation release*

### Changed
- `oe-eshop-db_migrate migrations:migrate` also executes migrations registered via the DI container
  (tag `oxid_esales.migration_path_provider`)

### Deprecated
- The whole package in favour of the OXID eShop console command `oe:database:migrate`
- `Migrations`
- `MigrationsBuilder`

### Fixed
- `migrations:generate` creates the missing migrations directory (`data` / `project_data`) instead of failing
- `migrations:migrate` no longer fails with a `TypeError` if the migrations directory does not exist

## v8.0.0-alpha.2 - 2026-02-12
*Compilation release*
