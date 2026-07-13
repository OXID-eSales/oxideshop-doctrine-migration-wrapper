# Change Log for OXID eShop doctrine migration integration

## v6.0.0 - Unreleased

### Changed
- `oe-eshop-db_migrate migrations:migrate` also executes migrations registered via the DI container
  (tag `oxid_esales.migration_path_provider`)

### Deprecated
- The whole package in favour of the OXID eShop console command `oe:database:migrate`
- `Migrations`
- `MigrationsBuilder`
