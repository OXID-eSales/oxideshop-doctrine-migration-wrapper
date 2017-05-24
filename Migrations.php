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

namespace OxidEsales\DoctrineMigrations;

use Symfony\Component\Console\Input\ArrayInput;

class Migrations
{
    /** @var  \OxidEsales\DoctrineMigrations\DoctrineApplicationBuilder $doctrineApplicationBuilder */
    private $doctrineApplicationBuilder;

    /** @var  \OxidEsales\DoctrineMigrations\ShopFacts\ShopFacts */
    private $eShopFacts;

    /** @var  \OxidEsales\DoctrineMigrations\$MigrationAvailabilityChecker */
    private $migrationAvailabilityChecker;

    /** @var string path to file which contains database configuration for Doctrine Migrations */
    private $dbFilePath;

    /**
     * Sets all needed dependencies.
     *
     * @param \OxidEsales\DoctrineMigrations\DoctrineApplicationBuilder $doctrineApplicationBuilder
     * @param \OxidEsales\DoctrineMigrations\ShopFacts\ShopFacts $shopFacts
     * @param string $dbFilePath
     * @param \OxidEsales\DoctrineMigrations\$MigrationAvailabilityChecker $migrationAvailabilityChecker
     */
    public function __construct($doctrineApplicationBuilder, $shopFacts, $dbFilePath, $migrationAvailabilityChecker)
    {
        $this->doctrineApplicationBuilder = $doctrineApplicationBuilder;
        $this->eShopFacts = $shopFacts;
        $this->dbFilePath = $dbFilePath;
        $this->migrationAvailabilityChecker = $migrationAvailabilityChecker;
    }

    public function execute($command)
    {
        $migrationPaths = $this->eShopFacts->getMigrationPaths();

        foreach ($migrationPaths as $migrationPath) {
            $doctrineApplication = $this->doctrineApplicationBuilder->build();

            $input = new ArrayInput([
                '--configuration' => $migrationPath,
                '--db-configuration' => $this->dbFilePath,
                '-n' => true,
                'command' => $command
            ]);

            if ($this->migrationAvailabilityChecker->migrationExists($migrationPath)) {
                $doctrineApplication->run($input);
            }
        }
    }
}
