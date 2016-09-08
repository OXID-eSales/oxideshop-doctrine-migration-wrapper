OXID eShop doctrine migration integration
=========================================

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

This component relies on information provided by
`eshop-edition_facts <https://github.com/OXID-eSales/eshop-edition_facts>`__
component in order to determine the current eShop installation edition. This
fact is taken from ``ESHOP_EDITION`` environment variable.

In order to bypass the above described logic and execute **only one specific
migration script suite** the environment variable ``MIGRATION_SUITE``
must not be empty! e.g. to execute only Enterprise edition migration script
suite use the following:

``MIGRATION_SUITE=EE ./vendor/bin/oe-eshop-facts oe-eshop-doctrine_migration``

The main script ``oe-eshop-doctrine_migration`` forwards all provided argument
values to the doctrine migration tool CLI. Here are few basic examples:

* ``./vendor/bin/oe-eshop-facts oe-eshop-doctrine_migration`` - Lists all
  available commands;
* ``./vendor/bin/oe-eshop-facts oe-eshop-doctrine_migration migrations:execute``
  - execute migrations;
* ``./vendor/bin/oe-eshop-facts oe-eshop-doctrine_migration migrations:generate``
  - generate new migration; (It's advised to use this together with
  ``MIGRATION_SUITE`` environment variable to ensure that only one
  migration suite will be populated with new migration script)

More information about **doctrine migration component** and it's CLI usage can
be found on their
`documentation page <http://docs.doctrine-project.org/projects/doctrine-migrations/en/latest/toc.html>`__.

OXID eShop doctrine migration integration related facts
=======================================================

This component provides eShop doctrine migration integration facts with the help
of `eshop-facts <https://github.com/OXID-eSales/eshop-facts>`__. Information
on how to use ``oe-eshop-doctrine_migration_facts`` script together with
``oe-eshop-facts`` can be found in the following
`README <https://github.com/OXID-eSales/eshop-facts/blob/master/README.rst>`__.

Output
------

The following information is provided after executing the script:

* ``ESHOP_DOCTRINE_MIGRATION_WRAPPER_VENDOR_PATH`` - Full path to current
  scripts directory;
* ``ESHOP_DOCTRINE_MIGRATION_BIN_PATH`` - Full path to 3rd party component's
  doctrine-migrations binary file;
* ``ESHOP_DOCTRINE_CONFIG_FILENAME`` - Filename used for migration
  configuration;
* ``ESHOP_DOCTRINE_DB_CONFIG_FILENAME`` - Filename used for database migration
  configuration;

|

* ``ESHOP_DOCTRINE_CE_MIGRATION_CONFIG_PATH`` - Full path to eShop CE migration
  configuration file;
* ``ESHOP_DOCTRINE_CE_MIGRATION_DATA_PATH`` - Full path to eShop CE migration
  files directory;
* ``ESHOP_DOCTRINE_CE_MIGRATION_DB_CONFIG_PATH`` - Full path to eShop CE
  database migration configuration file;
* ``ESHOP_DOCTRINE_CE_HAS_MIGRATIONS`` - "1" is given in case eShop CE has
  valid migration files present;

|

* ``ESHOP_DOCTRINE_PE_MIGRATION_CONFIG_PATH`` - Full path to eShop PE migration
  configuration file;
* ``ESHOP_DOCTRINE_PE_MIGRATION_DATA_PATH`` - Full path to eShop PE migration
  files directory;
* ``ESHOP_DOCTRINE_PE_MIGRATION_DB_CONFIG_PATH`` - Full path to eShop PE
  database migration configuration file;
* ``ESHOP_DOCTRINE_PE_HAS_MIGRATIONS`` - "1" is given in case eShop PE has valid
  migration files present;

|

* ``ESHOP_DOCTRINE_EE_MIGRATION_CONFIG_PATH`` - Full path to eShop EE migration
  configuration file;
* ``ESHOP_DOCTRINE_EE_MIGRATION_DATA_PATH`` - Full path to eShop EE migration
  files directory;
* ``ESHOP_DOCTRINE_EE_MIGRATION_DB_CONFIG_PATH`` - Full path to eShop EE
  database migration configuration file;
* ``ESHOP_DOCTRINE_EE_HAS_MIGRATIONS`` - "1" is given in case eShop EE has valid
  migration files present;

|

* ``ESHOP_DOCTRINE_PROJECT_MIGRATION_CONFIG_PATH`` - Full path to eShop project
  migration configuration file;
* ``ESHOP_DOCTRINE_PROJECT_MIGRATION_DATA_PATH`` - Full path to eShop project
  migration files directory;
* ``ESHOP_DOCTRINE_PROJECT_MIGRATION_DB_CONFIG_PATH`` - Full path to eShop
  project database migration configuration file;
* ``ESHOP_DOCTRINE_PROJECT_HAS_MIGRATIONS`` - "1" is given in case eShop project
  has valid migration files present.

Keep in mind that it's possible to override any variable from the list above
by providing it as an environment variable, e.g. to change the doctrine
migration binary path:

``ESHOP_DOCTRINE_MIGRATION_BIN_PATH=/usr/local/bin/doctrine-migration ./vendor/bin/oe-eshop-facts oe-eshop-doctrine_migration_facts``

Input
-----

The following environment variables are accepted:

* ``VERBOSE`` - Enables verbose mode which prints out all facts to ``STDOUT``;
* ``ESHOP_VERBOSE_DOCTRINE_MIGRATION_FACTS`` - Enables verbose mode only for the
  current script.
