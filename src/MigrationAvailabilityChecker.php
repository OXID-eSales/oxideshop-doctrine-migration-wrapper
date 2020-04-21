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
        $pathToMigrationsRootDirectory = dirname($pathToConfiguration);

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
}