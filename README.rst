OXID eShop doctrine migration integration
=========================================

.. image:: https://travis-ci.org/OXID-eSales/oxideshop-doctrine-migration-wrapper.svg?branch=master
    :target: https://travis-ci.org/OXID-eSales/oxideshop-doctrine-migration-wrapper

Document: https://docs.oxid-esales.com/developer/en/6.2/development/modules_components_themes/module/database_migration/index.html

Branch Compatibility
--------------------

* master branch is compatible with OXID eShop compilation master
* b-6.4.x branch is compatible with OXID eShop compilation 6.4.x
* b-6.3.x branch is compatible with OXID eShop compilation 6.3.x
* b-3.x branch is compatible with OXID eShop compilation 6.2.x
* b-1.x branch is compatible with OXID eShop compilations before 6.2.x

Description
-----------

OXID eShop uses database migrations for:

- eShop editions migration (CE, PE and EE)
- Project specific migrations
- Modules migrations

At the moment OXID eShop uses "Doctrine 2 Migrations" and it's integrated via OXID eShop migration components.

Doctrine Migrations runs migrations with a single configuration. But there was a need to run migration for one or all the
projects and modules (CE, PE, EE, PR and a specific module). For this reason `Doctrine Migration Wrapper` was created.

Running migrations - CLI
------------------------

Script to run migrations is installed within composer bin directory. It accepts two parameters:

- Doctrine Command
- Suite Type (CE, PE, EE, PR or a specific module_id)

.. code:: bash

   vendor/bin/oe-eshop-db_migrate <Doctrine_Command> <Suite_Type>

To get comprehensive information about Doctrine 2 Migrations and available commands as well, please see `official documentation <https://www.doctrine-project.org/projects/doctrine-migrations/en/2.2/index.html>`__.

Example:

.. code:: bash

   vendor/bin/oe-eshop-db_migrate migrations:migrate

This command will run all the migrations which are in OXID eShop specific directories. For example if you have
OXID eShop Enterprise edition, migration tool will run migrations in this order:

* Community Edition migrations (executed always)
* Professional Edition migrations (executed when eShop has PE or EE)
* Enterprise Edition migrations (executed when eShop has EE)
* Project specific migrations (executed always)
* Module migrations (executed when eShop has at least one module with migration)

.. _suite_types:

Suite Types (Generate migration for a single suite)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

It is also possible to run migrations for specific suite by defining `<Suite_Type>` parameter in the command.
This variable defines what type of migration it is. There are 5 suite types:

* **PR** - For project specific migrations. It should be always used for project development.
* **CE** - Generates migration file for OXID eShop Community Edition. It's used for product development only.
* **PE** - Generates migration file for OXID eShop Professional Edition. It's used for product development only.
* **EE** - Generates migration file for OXID eShop Enterprise Edition. It's used for product development only.
* **<module_id>** - Generates migration file for OXID eShop specific module. It’s used for module development only.

Example 1:

.. code:: bash

   vendor/bin/oe-eshop-db_migrate migrations:generate

This command generates migration versions for all the suite types.

Example 2:

.. code:: bash

   vendor/bin/oe-eshop-db_migrate migrations:generate EE

In this case it will be generated only for Enterprise Edition in `vendor/oxid-esales/oxideshop_ee/migration` directory.

Use Migrations Wrapper without CLI
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Doctrine Migration Wrapper is written in PHP and also could be used without command line interface. To do so:

- Create ``Migrations`` object with ``MigrationsBuilder->build()``
- Call ``execute`` method with needed parameters


Bugs and Issues
---------------

If you experience any bugs or issues, please report them in the section **OXID eShop (all versions)** of https://bugs.oxid-esales.com.
