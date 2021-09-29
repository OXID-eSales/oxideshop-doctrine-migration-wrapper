<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper\Tests\Integration;

use OxidEsales\Facts\Config\ConfigFile;
use PDO;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Setup database and file system for integration test.
 */
final class EnvironmentPreparator
{
    /** @var ConfigFile */
    private $configFile;
    /** @var PDO */
    private $databaseConnection;

    public function setupEnvironment(): void
    {
        $this->copySystemFiles();
        $this->configFile = new ConfigFile();
        $this->openDatabaseConnection();
        $this->setUpDatabase();
    }

    public function cleanEnvironment(): void
    {
        $this->destroyDatabase();
        $this->closeDatabaseConnection();
        $this->deleteSystemFiles();
    }

    private function openDatabaseConnection(): void
    {
        $this->databaseConnection = new PDO(
            "mysql:host={$this->configFile->dbHost}",
            $this->configFile->dbUser,
            $this->configFile->dbPwd
        );
    }

    private function setUpDatabase(): void
    {
        $databaseName = $this->configFile->dbName;
        $this->databaseConnection->exec("CREATE DATABASE `$databaseName`");
    }

    private function copySystemFiles(): void
    {
        $fileSystem = new Filesystem();
        $pathFromTestData = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'testData']);
        $pathToTestData = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..']);
        $fileSystem->mirror($pathFromTestData, $pathToTestData);
    }

    private function destroyDatabase(): void
    {
        $databaseName = $this->configFile->dbName;
        $this->databaseConnection->exec("DROP DATABASE `$databaseName`");
    }

    private function deleteSystemFiles(): void
    {
        $fileSystem = new Filesystem();
        $pathToSourceTestData = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'source']);
        $pathToVarTestData = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'var']);
        $fileSystem->remove($pathToSourceTestData);
        $fileSystem->remove($pathToVarTestData);
    }

    private function closeDatabaseConnection(): void
    {
        $this->databaseConnection = null;
    }
}
