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
    public function migrationExists($pathToConfiguration) {
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