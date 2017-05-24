<?php
use OxidEsales\DoctrineMigrations\Migrations;

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
    public function testCallsDoctrineMigrations()
    {
        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);
        $doctrineApplication->expects($this->atLeastOnce())->method('run');

        $doctrineApplicationBuilder = $this->getMock('DoctrineApplicationBuilder', ['build']);
        $doctrineApplicationBuilder->method('build')->will($this->returnValue($doctrineApplication));

        $shopFacts = $this->getMock('ShopFacts', ['getMigrationPaths']);
        $shopFacts->method('getMigrationPaths')->willReturn(['edition' => 'path_to_migrations']);

        $pathToDbConfig = '';

        $migrationAvailabilityChecker = $this->getMock('MigrationAvailabilityChecker', ['migrationExists']);
        $migrationAvailabilityChecker->method('migrationExists')->willReturn(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $pathToDbConfig, $migrationAvailabilityChecker);
        $migrations->execute('migrations:migrate');
    }

    public function testExecuteCEMigration()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';
        $migrationPaths = ['ce' => $ceMigrationsPath];

        $input = new ArrayInput([
            '--configuration' => $ceMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);
        $doctrineApplication->expects($this->atLeastOnce())->method('run')->with($input);

        $doctrineApplicationBuilder = $this->getMock('DoctrineApplicationBuilder', ['build']);
        $doctrineApplicationBuilder->method('build')->will($this->returnValue($doctrineApplication));

        $shopFacts = $this->getMock('ShopFacts', ['getMigrationPaths']);
        $shopFacts->method('getMigrationPaths')->willReturn($migrationPaths);

        $migrationAvailabilityChecker = $this->getMock('MigrationAvailabilityChecker', ['migrationExists']);
        $migrationAvailabilityChecker->method('migrationExists')->willReturn(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }

    /**
     * Tests that migrations for editions are called in a right order:
     * First CE, then PE, then EE.
     */
    public function testExecuteEEMigration()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';
        $peMigrationsPath = 'path_to_pe_migrations';
        $eeMigrationsPath = 'path_to_ee_migrations';
        $migrationPaths = [
            'ce' => 'path_to_ce_migrations',
            'pe' => 'path_to_pe_migrations',
            'ee' => 'path_to_ee_migrations',
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

        $doctrineApplicationBuilder = $this->getMock('DoctrineApplicationBuilder', ['build']);
        $doctrineApplicationBuilder->method('build')->will($this->returnValue($doctrineApplication));

        $shopFacts = $this->getMock('ShopFacts', ['getMigrationPaths']);
        $shopFacts->method('getMigrationPaths')->willReturn($migrationPaths);

        $migrationAvailabilityChecker = $this->getMock('MigrationAvailabilityChecker', ['migrationExists']);
        $migrationAvailabilityChecker->method('migrationExists')->willReturn(true);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }

    public function testSkipMigrationWhenItDoesNotExist()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';
        $migrationPaths = ['ce' => $ceMigrationsPath];

        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);
        $doctrineApplication->expects($this->never())->method('run');

        $doctrineApplicationBuilder = $this->getMock('DoctrineApplicationBuilder', ['build']);
        $doctrineApplicationBuilder->method('build')->will($this->returnValue($doctrineApplication));

        $shopFacts = $this->getMock('ShopFacts', ['getMigrationPaths']);
        $shopFacts->method('getMigrationPaths')->willReturn($migrationPaths);

        $migrationAvailabilityChecker = $this->getMock('MigrationAvailabilityChecker', ['migrationExists']);
        $migrationAvailabilityChecker->method('migrationExists')->willReturn(false);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }

    public function testMigrationAvailabilityCheckerCalledWithCorrectPath()
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';
        $migrationPaths = ['ce' => $ceMigrationsPath];

        $doctrineApplication = $this->getMock('DoctrineApplicationWrapper', ['run']);

        $doctrineApplicationBuilder = $this->getMock('DoctrineApplicationBuilder', ['build']);
        $doctrineApplicationBuilder->method('build')->will($this->returnValue($doctrineApplication));

        $shopFacts = $this->getMock('ShopFacts', ['getMigrationPaths']);
        $shopFacts->method('getMigrationPaths')->willReturn($migrationPaths);

        $migrationAvailabilityChecker = $this->getMock('MigrationAvailabilityChecker', ['migrationExists']);
        $migrationAvailabilityChecker->expects($this->atLeastOnce())->method('migrationExists')->with($ceMigrationsPath);

        $migrations = new Migrations($doctrineApplicationBuilder, $shopFacts, $dbConfigFilePath, $migrationAvailabilityChecker);

        $migrations->execute($command);
    }
}
