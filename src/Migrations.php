<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use Doctrine\Migrations\Exception\MigrationClassNotFound;
use Symfony\Component\Console\Application;
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
     */
    public function execute(?string $command, ?string $edition = null, array $flags = []): int
    {
        $command = (string)$command;
        $migrationPaths = $this->migrationsPathProvider->getMigrationsPath($edition);
        $this->validateFlags($flags);

        foreach ($migrationPaths as $suite => $migrationPath) {
            $suite = strtoupper($suite);
            if ($this->shouldRunCommand($command, $migrationPath)) {
                $doctrineApplication = $this->doctrineApplicationBuilder->build();
                $input = $this->formDoctrineInput($command, $migrationPath, $this->dbFilePath, $flags);
                try {
                    if ($command && $suite) {
                        $this->addSuiteToCommandName($doctrineApplication, $command, $suite);
                    }
                    $errorCode = $doctrineApplication->run($input, $this->output);
                    if ($suite && $this->isMigrationsGenerateCommand($command)) {
                        $this->appendSuiteInfoAfterHelpMessageOutput($suite);
                    }
                } catch (MigrationClassNotFound $exception) {
                    throw new MigrationClassNotFound(
                        "Error running migration for suite type '$suite': " .
                        $exception->getMessage()
                    );
                }
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
     * @param array $flags flags for command
     *
     * @return ArrayInput
     */
    private function formDoctrineInput(string $command, string $migrationPath, string $dbFilePath, array $flags): ArrayInput
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

    private function validateFlags(array $flags): void
    {
        $notAllowedFlags = array_filter($this->predefinedCommandKeys, function ($var) use ($flags) {
            return array_key_exists($var, $flags);
        });

        if (!empty($notAllowedFlags)) {
            throw new \Symfony\Component\Console\Exception\InvalidOptionException(
                'The following flags are not allowed to be overwritten: ' . implode(', ', $notAllowedFlags)
            );
        }
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
    private function shouldRunCommand(string $command, $migrationPath): bool
    {
        return ($command !== self::MIGRATE_COMMAND
            || $this->migrationAvailabilityChecker->migrationExists($migrationPath));
    }

    private function addSuiteToCommandName(Application $doctrineApplication, string $command, string $suite): void
    {
        $commandObject = $doctrineApplication->get($command);
        $commandObject->setName($commandObject->getName() . " $suite");
    }

    private function isMigrationsGenerateCommand(string $command): bool
    {
        return $command === 'migrations:generate';
    }

    private function appendSuiteInfoAfterHelpMessageOutput(string $suite): void
    {
        ($this->output ?? new ConsoleOutput())->writeln(
            " Don't forget to add the correct Suite_Type to the above commands <info>migrations:execute $suite [options] [--] <versions>...</info>\n"
        );
    }
}
