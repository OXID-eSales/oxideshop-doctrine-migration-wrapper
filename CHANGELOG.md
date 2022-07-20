# Change Log for OXID eShop doctrine migration integration

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/ ).
and this project adheres to [Semantic Versioning](http://semver.org/ ).

## [3.4.0] - 2022-07-20

### Fixed
- Remove useless dependency on composer/package-versions-deprecated [PR-7](https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/pull/7)

## [3.3.0] - 2021-11-26

### Removed
- Support for PHP 7.3

### Fixed
- Update tests

## [3.2.0] - 2021-04-12

### Added
- Support PHP 8.0

### Removed
- Support PHP 7.1 and 7.2

## [3.1.1] - 2020-11-12

### Fixed

- Ensure Composer V2 compatibility for PHP 7.1-7.3 via composer/package-versions-deprecated

## [3.1.0] - 2020-07-03

### Added

- Add module migrations

## [3.0.0] - 2020-04-24

### Changed
- Minimum PHP version to v7.1
- Minimum PHPUnit version to v6.*

### Fixed
- Made composer.json compatible with composer v2
- Running command without arguments

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

[3.4.0]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v3.3.0...v3.4.0
[3.3.0]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v3.2.0...v3.3.0
[3.2.0]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v3.1.1...v3.2.0
[3.1.1]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v3.1.0...v3.1.1
[3.1.0]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v3.0.0...v3.1.0
[3.0.0]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v2.1.3...v3.0.0
[2.1.3]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v2.1.2...v2.1.3
[2.1.2]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v2.1.1...v2.1.2
[2.1.1]: https://github.com/OXID-eSales/oxideshop-doctrine-migration-wrapper/compare/v2.1.0...v2.1.1
