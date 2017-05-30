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

namespace OxidEsales\DoctrineMigrations\Tests\Unit;

use OxidEsales\DoctrineMigrations\Migrations;
use Symfony\Component\Console\Input\ArrayInput;

class MigrationsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check if Doctrine Application mock is called
     * when migrations are available.
     */
    public function testCallsDoctrineMigrations()
    {
        $doctrineApplication = $this->getDoctrineMock(true);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub(['edition' => 'path_to_migrations']);

        $pathToDbConfig = '';

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $pathToDbConfig, $migrationAvailabilityChecker);
        $migrations->execute('migrations:migrate');
    }

    /**
     * Check if Doctrine Application mock is called with right parameters
     * when migrations are available.
     */
    public function testExecuteCEMigration()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';

        $input = new ArrayInput([
            '--configuration' => $ceMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $doctrineApplication = $this->getDoctrineMock(true, $input);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub(['ce' => $ceMigrationsPath]);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }

    /**
     * Tests that all migrations are called what's defined in a Shop facts
     * with an order from ShopFacts.
     */
    public function testExecuteAllMigrations()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';
        $peMigrationsPath = 'path_to_pe_migrations';
        $eeMigrationsPath = 'path_to_ee_migrations';
        $migrationPaths = [
            'ce' => $ceMigrationsPath,
            'pe' => $peMigrationsPath,
            'ee' => $eeMigrationsPath,
        ];

        $inputCE = new ArrayInput([
            '--configuration' => $ceMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $inputPE = new ArrayInput([
            '--configuration' => $peMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $inputEE = new ArrayInput([
            '--configuration' => $eeMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);
        $doctrineApplication->expects($this->at(0))->method('run')->with($inputCE);
        $doctrineApplication->expects($this->at(1))->method('run')->with($inputPE);
        $doctrineApplication->expects($this->at(2))->method('run')->with($inputEE);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub($migrationPaths);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }

    /**
     * Tests that only requested migration is called even when more migrations exist.
     * Does testing by calling migration in different case sensitivity.
     */
    public function testExecuteOnlyRequestedMigration()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $eeMigrationsPath = 'path_to_ee_migrations';
        $migrationPaths = [
            'ce' => 'path_to_ce_migrations',
            'pe' => 'path_to_pe_migrations',
            'eE' => $eeMigrationsPath,
        ];

        $inputEE = new ArrayInput([
            '--configuration' => $eeMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);
        $doctrineApplication->expects($this->once())->method('run')->with($inputEE);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub($migrationPaths);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command, 'Ee');
    }

    /**
     * Tests that no error appears when no migrations exist for requested edition.
     */
    public function testNoErrorWhenNoMigrationExistForRequestedEdition()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $migrationPaths = [
            'ce' => 'path_to_ce_migrations',
            'pe' => 'path_to_pe_migrations',
            'ee' => 'path_to_ee_migrations',
        ];

        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);
        $doctrineApplication->expects($this->never())->method('run');

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub($migrationPaths);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command, 'PR');
    }

    /**
     * Check if Doctrine Application mock is NOT called
     * when migrations are NOT available.
     */
    public function testSkipMigrationWhenItDoesNotExist()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';

        $doctrineApplication = $this->getDoctrineMock(false);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub(['ce' => $ceMigrationsPath]);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(false);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }

    /**
     * Check if migrations availability checker is called with a right parameter.
     */
    public function testMigrationAvailabilityCheckerCalledWithCorrectPath()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';

        $doctrineApplication = $this->getDoctrineStub();

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub(['ce' => $ceMigrationsPath]);

        $migrationAvailabilityChecker = $this->getMock('MigrationAvailabilityChecker', ['migrationExists']);
        $migrationAvailabilityChecker->expects($this->atLeastOnce())->method('migrationExists')->with($ceMigrationsPath);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }

    /**
     * Check if generates new migration when no migration exist in a folder.
     */
    public function testRunGenerateMigrationCommandEvenIfNoMigrationExist()
    {
        $command = 'migrations:generate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';

        $doctrineApplication = $this->getDoctrineMock(true);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub(['ce' => $ceMigrationsPath]);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(false);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }

    /**
     * Test to check if error code is passed from Doctrine to upper caller.
     */
    public function testReturnErrorCodeWhenMigrationFail()
    {
        $errorCode = 1;

        $doctrineApplication = $this->getDoctrineStub($errorCode);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $shopFacts = $this->getShopFactsStub(['edition' => 'path_to_migrations']);

        $pathToDbConfig = '';

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $pathToDbConfig, $migrationAvailabilityChecker);

        $this->assertSame($errorCode, $migrations->execute('migrations:migrate'));
    }

    /**
     * Create mock for Doctrine Application.
     *
     * @param bool $runsAtLeastOnce
     * @param string $callWith
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDoctrineMock($runsAtLeastOnce, $callWith = null)
    {
        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);

        if ($runsAtLeastOnce && is_null($callWith)) {
            $doctrineApplication->expects($this->atLeastOnce())->method('run');
        } elseif($runsAtLeastOnce) {
            $doctrineApplication->expects($this->atLeastOnce())->method('run')->with($callWith);
        } else {
            $doctrineApplication->expects($this->never())->method('run');
        }

        return $doctrineApplication;
    }

    /**
     * Stub 3rd party dependency.
     *
     * @param int $result what error code to return.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDoctrineStub($result = 0)
    {
        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);
        $doctrineApplication->method('run')->will($this->returnValue($result));

        return $doctrineApplication;
    }

    /**
     * Stub builder to get needed application mock.
     *
     * @param $doctrineApplication
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDoctrineApplicationBuilderStub($doctrineApplication)
    {
        $doctrineApplicationBuilder = $this->getMock('DoctrineApplicationBuilder', ['build']);
        $doctrineApplicationBuilder->method('build')->will($this->returnValue($doctrineApplication));

        return $doctrineApplicationBuilder;
    }

    /**
     * Stub Facts to get paths needed for tests.
     *
     * @param array $migrationPaths paths to migrations
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getShopFactsStub($migrationPaths)
    {
        $shopFacts = $this->getMock('ShopFacts', ['getMigrationPaths']);
        $shopFacts->method('getMigrationPaths')->willReturn($migrationPaths);

        return $shopFacts;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getMigrationAvailabilityStub($ifMigrationsAvailable)
    {
        $migrationAvailabilityChecker = $this->getMock('MigrationAvailabilityChecker', ['migrationExists']);
        $migrationAvailabilityChecker->method('migrationExists')->willReturn($ifMigrationsAvailable);

        return $migrationAvailabilityChecker;
    }
}
