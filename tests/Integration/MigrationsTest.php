<?php

/**
 * This file is part of OXID eSales Doctrine Migration Wrapper.
 *
 * OXID eSales Doctrine Migration Wrapper is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eSales Doctrine Migration Wrapper is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eSales Doctrine Migration Wrapper. If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper\Tests\Integration;

use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\Facts\Config\ConfigFile;
use PHPUnit\Framework\TestCase;

final class MigrationsTest extends TestCase
{
    /** @var ConfigFile */
    private $configFile;

    /** @var EnvironmentPreparator */
    private $environmentPreparator;

    public function __construct()
    {
        $this->environmentPreparator = new EnvironmentPreparator();
        parent::__construct();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->environmentPreparator->setupEnvironment();
        $this->configFile = new ConfigFile();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->environmentPreparator->cleanEnvironment();
    }

    /**
     * Run migration for one edition and one project to test that they works.
     * Tests that:
     * - integration with Doctrine Migration actually works
     * - it is possible to run two migrations in a row
     * - Migration Builder actually works
     */
    public function testMigrateSuccess(): void
    {
        $migration = (new MigrationsBuilder())->build();
        $migration->execute('migrations:migrate');

        $databaseName = $this->configFile->dbName;
        $databaseConnection = new \PDO(
            'mysql:host=' . $this->configFile->dbHost,
            $this->configFile->dbUser,
            $this->configFile->dbPwd
        );

        $result = $databaseConnection->query(
            "SELECT id as entries FROM `$databaseName`.`test_doctrine_migration_wrapper`"
        );
        $this->assertSame(2, $result->rowCount(), 'There must be one row for shop migration and one for project.');

        $result = $databaseConnection->query(
            "SELECT 1 FROM `$databaseName`.`test_doctrine_migration_wrapper` WHERE id = 'shop_migration'"
        );
        $this->assertSame(1, $result->rowCount(), 'There must be one row for shop migration');

        $result = $databaseConnection->query(
            "SELECT 1 FROM `$databaseName`.`test_doctrine_migration_wrapper` WHERE id = 'project_migration'"
        );
        $this->assertSame(1, $result->rowCount(), 'There must be one row for project migration');
    }
}
