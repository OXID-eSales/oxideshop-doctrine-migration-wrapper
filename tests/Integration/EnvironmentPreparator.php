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

use OxidEsales\Facts\Config\ConfigFile;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Setup database and file system for integration test.
 */
class EnvironmentPreparator
{
    /** @var ConfigFile */
    private $configFile = null;

    public function setupEnvironment()
    {
        $this->copySystemFiles();
        $this->configFile = new ConfigFile();
        $this->openDatabaseConnection();
        $this->setUpDatabase();
    }

    public function cleanEnvironment()
    {
        $this->destroyDatabase();
        $this->closeDatabaseConnection();
        $this->deleteSystemFiles();
    }

    private function openDatabaseConnection()
    {
        $this->databaseConnection = new \PDO('mysql:host=' . $this->configFile->dbHost, $this->configFile->dbUser, $this->configFile->dbPwd);
    }

    private function setUpDatabase()
    {
        $databaseName = $this->configFile->dbName;
        $this->databaseConnection->query("CREATE DATABASE `$databaseName`");
    }

    private function copySystemFiles()
    {
        $fileSystem = new Filesystem();
        $pathFromTestData = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'testData']);
        $pathToTestData = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']);
        $fileSystem->mirror($pathFromTestData, $pathToTestData);
    }

    private function destroyDatabase()
    {
        $databaseName = $this->configFile->dbName;
        $this->databaseConnection->query("DROP DATABASE `$databaseName`");
    }

    private function deleteSystemFiles()
    {
        $fileSystem = new Filesystem();
        $pathToTestData = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'source']);
        $fileSystem->remove($pathToTestData);
    }

    private function closeDatabaseConnection()
    {
        $this->databaseConnection = null;
    }
}