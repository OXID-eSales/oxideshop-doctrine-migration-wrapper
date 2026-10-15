<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

class MigrationAvailabilityChecker
{
    /**
     * Check if migrations exist.
     * At least one file for migrations must exist.
     * For example configuration exists, but no migration exist yet would result false.
     *
     * @param string $pathToConfiguration path to file which describes configuration for Doctrine Migrations.
     *
     * @return bool
     */
    public function migrationExists($pathToConfiguration)
    {
        if (!is_file($pathToConfiguration)) {
            return false;
        }

        $pathToMigrationsDirectory = $this->getPathToMigrations($pathToConfiguration);

        if ($this->atLeastOneMigrationFileExist($pathToMigrationsDirectory)) {
            return true;
        }

        return false;
    }

    /**
     * Find path to migration directory.
     * Different path returned for a project migrations.
     *
     * @param string $pathToConfiguration
     *
     * @return string
     */
    private function getPathToMigrations($pathToConfiguration)
    {
        $pathToMigrationsRootDirectory = \dirname($pathToConfiguration);

        $pathToMigrationsDirectory = $pathToMigrationsRootDirectory . DIRECTORY_SEPARATOR . 'data';
        if (strpos($pathToConfiguration, 'project_migrations')) {
            $pathToMigrationsDirectory = $pathToMigrationsRootDirectory . DIRECTORY_SEPARATOR . 'project_data';
        }

        return $pathToMigrationsDirectory;
    }

    /**
     * Check if at least one migration file exist by ignoring other files:
     * - upper directory indicator
     * - .gitkeep which might exist in a directory to keep it in a version system
     *
     * @param string $pathToMigrationsDirectory
     *
     * @return bool
     */
    private function atLeastOneMigrationFileExist($pathToMigrationsDirectory)
    {
        if (!is_dir($pathToMigrationsDirectory)) {
            return false;
        }

        $notMigrationFiles = [
            '.',
            '..'
        ];

        if (file_exists($pathToMigrationsDirectory . DIRECTORY_SEPARATOR . '.gitkeep')) {
            $notMigrationFiles[] = '.gitkeep';
        }

        $atLeastOneMigrationExist = count(scandir($pathToMigrationsDirectory)) > count($notMigrationFiles);

        return $atLeastOneMigrationExist;
    }

    /**
     * Check if directory data exists by using the Configuration-File-Path
     * If the directory does not exist it gets created
     *
     * @param string $pathToConfiguration
     *
     * @return void
     */
    public function ensureMigrationsDirectoryExists($pathToConfiguration): void
    {
        if (!is_file($pathToConfiguration)) {
            return;
        }

        $directory = $this->getPathToMigrations($pathToConfiguration);

        if (is_dir($directory)) {
            return;
        }

        if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" could not be created', $directory));
        }
    }
}
