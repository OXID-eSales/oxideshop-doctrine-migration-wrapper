<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
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

    /** @var  \OxidEsales\DoctrineMigrationWrapper\$MigrationAvailabilityChecker */
    private $migrationAvailabilityChecker;

    /** @var string path to file which contains database configuration for Doctrine Migrations */
    private $dbFilePath;

    /** Command for doctrine to run database migrations. */
    const MIGRATE_COMMAND = 'migrations:migrate';

    private const STATUS_COMMAND = 'migrations:status';

    /** @var Output Add a possibility to provide a custom output handler */
    private $output;

    /**
     * @var MigrationsPathProvider
     */
    private $migrationsPathProvider;

    /**
     * @var string[]
     */
    private $predefinedCommandKeys = [
        'configuration' => '--configuration',
        'dbConfiguration' => '--db-configuration',
        'noInteraction' => '-n'
    ];

    /**
     *
     * @param $doctrineApplicationBuilder
     * @param $dbFilePath
     * @param $migrationAvailabilityChecker
     * @param $migrationsPathProvider
     */
    public function __construct(
        $doctrineApplicationBuilder,
        $dbFilePath,
        $migrationAvailabilityChecker,
        $migrationsPathProvider
    ) {
        $this->doctrineApplicationBuilder = $doctrineApplicationBuilder;
        $this->dbFilePath = $dbFilePath;
        $this->migrationAvailabilityChecker = $migrationAvailabilityChecker;
        $this->migrationsPathProvider = $migrationsPathProvider;
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
     * @param string|null $command Doctrine Migration command to run.
     * @param string|null $edition Possibility to run migration only against one edition.
     * @param array $flags Doctrine Migration flags.
     *
     * @return int|string error code or error if one exist or 0 for success
     */
    public function execute(?string $command, ?string $edition = null, array $flags = [])
    {
        $migrationPaths = $this->migrationsPathProvider->getMigrationsPath($edition);
        $flagsValidationError = $this->validateFlags($flags);
        if ($flagsValidationError) {
            return $flagsValidationError;
        }

        foreach ($migrationPaths as $migrationEdition => $migrationPath) {
            $doctrineApplication = $this->doctrineApplicationBuilder->build();

            $input = $this->formDoctrineInput($command, $migrationPath, $this->dbFilePath, $flags);

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
     * @param string|null $command command to run.
     * @param string $migrationPath path to migration configuration file.
     * @param string $dbFilePath path to database configuration file.
     * @param array $flags flags for command
     *
     * @return ArrayInput
     */
    private function formDoctrineInput(?string $command, string $migrationPath, string $dbFilePath, array $flags): ArrayInput
    {
        $formedInput = [
            $this->predefinedCommandKeys['configuration'] => $migrationPath,
            $this->predefinedCommandKeys['dbConfiguration'] => $dbFilePath,
            $this->predefinedCommandKeys['noInteraction'] => true,
            'command' => !empty($command) ? $command : self::STATUS_COMMAND,
        ];

        $formedInput = array_merge($formedInput, $flags);

        return new ArrayInput($formedInput);
    }

    private function validateFlags(array $flags): ?string
    {
        $notAllowedFlags = array_filter($this->predefinedCommandKeys, function ($var) use(&$flags) {
            return array_key_exists($var, $flags);
        });

        if (!empty($notAllowedFlags)) {
            return 'The following flags are not allowed: "'.implode('","', $notAllowedFlags)."\"\n";
        }

        return null;
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
}
