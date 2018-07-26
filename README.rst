OXID eShop doctrine migration integration
=========================================

.. image:: https://travis-ci.org/OXID-eSales/oxideshop-doctrine-migration-wrapper.svg?branch=master
    :target: https://travis-ci.org/OXID-eSales/oxideshop-doctrine-migration-wrapper

Current component allows to execute **doctrine migration binary** for active
eShop edition and with database configured from the eShop installation itself.
The following different logic is applied during the execution of different
migration scripts suites within different eShop editions:

* **Community edition migrations** - executed always;
* **Professional edition migrations** - executed when eShop has Professional
  edition or Enterprise edition;
* **Enterprise edition migrations** - executed when eShop has Enterprise
  edition;
* **Project specific migrations** - executed always;

Keep in mind that the migration suite is executed only when it is able to find
**at least one** migration script.

Recommended way to update your OXID eShop
-----------------------------------------

vendor/bin/oe-eshop-db_migrate migrations:migrate

Possible ways to use
--------------------
- Use composer command oe:migration:run to run exsting migrations.
- Use composer command oe:migration:new to generate new migration.
- Run bash script: ``vendor/bin/oe-eshop-doctrine_migration`` to run existing migrations.
- Run bash script: ``vendor/bin/oe-eshop-doctrine_migration`` DOCTRINE_COMMAND to execute specific command.
- Run PHP script ``vendor/oxid-esales/migrate.php migrations:migrate`` to run existing migrations.
- Run PHP script ``vendor/oxid-esales/migrate.php`` DOCTRINE_COMMAND to execute specific command.
- Use class ``Migrations`` or ``MigrationsBuilder``

Bugs and Issues
---------------

If you experience any bugs or issues, please report them in the section **OXID eShop (all versions)** of https://bugs.oxid-esales.com.
