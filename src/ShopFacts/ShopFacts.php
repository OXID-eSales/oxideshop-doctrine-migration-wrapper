<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\DoctrineMigrations\ShopFacts;

use OxidEsales\DoctrineMigrations\Config\ConfigFile;
use OxidEsales\DoctrineMigrations\Edition\EditionSelector;

class ShopFacts
{
    public function __construct()
    {
        $this->configReader = new ConfigFile();
    }

    public function getDatabaseName()
    {
        return $this->configReader->dbName;
    }

    public function getDatabaseUserName()
    {
        return $this->configReader->dbUser;
    }

    public function getDatabasePassword()
    {
        return $this->configReader->dbPwd;
    }

    public function getDatabaseHost()
    {
        return $this->configReader->dbHost;
    }

    public function getDatabaseDriver()
    {
        return $this->configReader->dbType;
    }

    public function getMigrationPaths()
    {
        $editionSelector = new EditionSelector();

        $migrationPaths = [
            'ce' => $this->configReader->getVar(ConfigFile::PARAMETER_SOURCE_PATH).'/migration/migrations.yml',
        ];

        if ($editionSelector->isProfessional() || $editionSelector->isEnterprise()) {
            $migrationPaths['pe'] = $this->configReader->getVar(ConfigFile::PARAMETER_VENDOR_PATH)
                                    . '/oxid-esales/oxideshop-pe/migration/migrations.yml';
        }

        if ($editionSelector->isEnterprise()) {
            $migrationPaths['ee'] = $this->configReader->getVar(ConfigFile::PARAMETER_VENDOR_PATH)
                                    .'/oxid-esales/oxideshop-ee/migration/migrations.yml';
        }

        $migrationPaths['pr'] = $this->configReader->getVar(ConfigFile::PARAMETER_SOURCE_PATH)
                                    . '/migration/project_migrations.yml';

        return $migrationPaths;
    }
}
