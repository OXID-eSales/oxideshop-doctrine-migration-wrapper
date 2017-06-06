<?php
/**
 * Created by PhpStorm.
 * User: saulius stasiukaitis
 * Date: 6/6/2017
 * Time: 10:53 AM
 */

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
        $this->databaseConnection = new \mysqli($this->configFile->dbHost, $this->configFile->dbUser, $this->configFile->dbPwd);
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
        mysqli_close($this->databaseConnection);
    }
}