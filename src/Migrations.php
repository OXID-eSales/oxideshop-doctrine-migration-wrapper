<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use OxidEsales\Facts\Facts;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\Output;

/**
 * Class to run Doctrine Migration commands.
 * OXID eShop might have several migrations to run for different edition and project.
 * This class ensures that all needed migrations run.
 */
class Migrations
{
    /** @var  DoctrineApplicationBuilder $doctrineApplicationBuilder */
    private $doctrineApplicationBuilder;

    /** @var  Facts */
    private $facts;

    /** @var  \OxidEsales\DoctrineMigrationWrapper\$MigrationAvailabilityChecker */
    private $migrationAvailabilityChecker;

    /** @var string path to file which contains database configuration for Doctrine Migrations */
    private $dbFilePath;

    /** Command for doctrine to run database migrations. */
    const MIGRATE_COMMAND = 'migrations:migrate';

    private const STATUS_COMMAND = 'migrations:status';

    /** @var Output Add a possibility to provide a custom output handler */
    private $output = null;

    /**
     * Sets all needed dependencies.
     *
     * @param DoctrineApplicationBuilder $doctrineApplicationBuilder
     * @param Facts $facts
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
    private function formDoctrineInput($command, $migrationPath, $dbFilePath): ArrayInput
    {
        return new ArrayInput([
            '--configuration' => $migrationPath,
            '--db-configuration' => $dbFilePath,
            '-n' => true,
            'command' => !empty($command) ? $command : self::STATUS_COMMAND,
        ]);
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
