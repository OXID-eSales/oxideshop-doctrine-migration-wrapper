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

namespace OxidEsales\DoctrineMigrationWrapper;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\Output;

/**
 * Class to run Doctrine Migration commands.
 * OXID eShop might have several migrations to run for different edition and project.
 * This class ensures that all needed migrations run.
 */
class Migrations
{
    /** @var  \OxidEsales\DoctrineMigrationWrapper\DoctrineApplicationBuilder $doctrineApplicationBuilder */
    private $doctrineApplicationBuilder;

    /** @var  \OxidEsales\Facts\Facts */
    private $facts;

    /** @var  \OxidEsales\DoctrineMigrationWrapper\$MigrationAvailabilityChecker */
    private $migrationAvailabilityChecker;

    /** @var string path to file which contains database configuration for Doctrine Migrations */
    private $dbFilePath;

    /** Command for doctrine to run database migrations. */
    const MIGRATE_COMMAND = 'migrations:migrate';

    /** @var Output Add a possibility to provide a custom output handler */
    private $output = null;

    /**
     * Sets all needed dependencies.
     *
     * @param \OxidEsales\DoctrineMigrationWrapper\DoctrineApplicationBuilder $doctrineApplicationBuilder
     * @param \OxidEsales\Facts\Facts $facts
     * @param string $dbFilePath
     * @param \OxidEsales\DoctrineMigrationWrapper\$MigrationAvailabilityChecker $migrationAvailabilityChecker
     */
    public function __construct($doctrineApplicationBuilder, $facts, $dbFilePath, $migrationAvailabilityChecker)
    {
        $this->doctrineApplicationBuilder = $doctrineApplicationBuilder;
        $this->facts = $facts;
        $this->dbFilePath = $dbFilePath;
        $this->migrationAvailabilityChecker = $migrationAvailabilityChecker;
    }

    /**
     * @param Output $output Add a possibility to provide a custom output handler
     */
    public function setOutput(Output $output = null)
    {
        $this->output = $output;
    }

    /**
     * Execute Doctrine Migration command for all needed Shop edition and project.
     * If Doctrine returns an error code breaks and return it.
     *
     * @param string $command Doctrine Migration command to run.
     * @param string $edition Possibility to run migration only against one edition.
     *
     * @return int error code if one exist or 0 for success
     */
    public function execute($command, $edition = null)
    {
        $migrationPaths = $this->getMigrationPaths($edition);

        foreach ($migrationPaths as $migrationEdition => $migrationPath) {

            $doctrineApplication = $this->doctrineApplicationBuilder->build();

            $input = $this->formDoctrineInput($command, $migrationPath, $this->dbFilePath);

            if ($this->shouldRunCommand($command, $migrationPath)) {
                $errorCode = $doctrineApplication->run($input, $this->output);
                if ($errorCode) {
                    return $errorCode;
                }
            }
        }

        return 0;
    }

    /**
     * Form input which is expected by Doctrine.
     *
     * @param string $command command to run.
     * @param string $migrationPath path to migration configuration file.
     * @param string $dbFilePath path to database configuration file.
     *
     * @return ArrayInput
     */
    private function formDoctrineInput($command, $migrationPath, $dbFilePath)
    {
        $input = new ArrayInput([
            '--configuration' => $migrationPath,
            '--db-configuration' => $dbFilePath,
            '-n' => true,
            'command' => $command
        ]);
        return $input;
    }

    /**
     * Check if command should be performed:
     * - All commands should be performed without additional check except migrate
     * - Migrate command should be performed only if actual migrations exist.
     *
     * @param string $command command to run.
     * @param string $migrationPath path to migration configuration file.
     *
     * @return bool
     */
    private function shouldRunCommand($command, $migrationPath)
    {
        return ($command !== self::MIGRATE_COMMAND
            || $this->migrationAvailabilityChecker->migrationExists($migrationPath));
    }

    /**
     * Filters out only needed migrations.
     *
     * @param string $edition Shop edition.
     *
     * @return array
     */
    private function getMigrationPaths($edition = null)
    {
        $allMigrationPaths = $this->facts->getMigrationPaths();

        if (is_null($edition)) {
            return $allMigrationPaths;
        }

        $migrationPaths = [];
        foreach ($allMigrationPaths as $migrationEdition => $migrationPath) {
            if (strtolower($migrationEdition) === strtolower($edition)) {
                $migrationPaths[$migrationEdition] = $migrationPath;
                break;
            }
        }

        return $migrationPaths;
    }
}
