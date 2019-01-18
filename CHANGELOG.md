# Change Log for OXID eShop doctrine migration integration

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/ ).
and this project adheres to [Semantic Versioning](http://semver.org/ ).

## [2.1.4] - unreleased

### Changed

### Fixed

## [2.1.3] - 2019-01-18

### Changed
- Exclude non-essential files from dist package [PR-5](https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/pull/5)
- Allow different output types as output handler [PR-4](https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/pull/4)

## [2.1.2] - 2018-03-29 

### Fixed
- Database port is used now when creating database connection for migrations.

## [2.1.1] - 2018-03-12

### Changed

- Pdo_mysql is used instead of mysqli as a database driver. 

### Fixed

- No more illegal mix of collation errors if collation_server was configured to something else than utf8_general_ci. [Bug 6782](https://bugs.oxid-esales.com/view.php?id=6782)

[2.1.4]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v2.1.3...HEAD
[2.1.3]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v2.1.2...v2.1.3
[2.1.2]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v2.1.1...v2.1.2
[2.1.1]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v2.1.0...v2.1.1
