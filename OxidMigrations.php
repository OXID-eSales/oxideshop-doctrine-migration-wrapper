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

use OxidEsales\DoctrineMigrations\Config\ConfigFile;
use OxidEsales\DoctrineMigrations\Edition\EditionSelector;
use Symfony\Component\Console\Input\ArrayInput;

class OxidMigrations
{
    const MIGRATE_COMMAND = 'migrations:migrate';

    public function execute($command = null)
    {
        if (is_null($command)) {
            $command = self::MIGRATE_COMMAND;
        }

        $configs = new ConfigFile();
        $cliConfigurationValues = [
            '--configuration' => $configs->getVar(ConfigFile::PARAMETER_SOURCE_PATH).'/migration/migrations.yml',
            '--db-configuration' => __DIR__.DIRECTORY_SEPARATOR.'migrations-db.php',
            '-n' => true,
            'command' => $command
        ];

        $inputCE = $cliConfigurationValues;
        $inputPE = array_merge($cliConfigurationValues, ['--configuration' => $configs->getVar(ConfigFile::PARAMETER_VENDOR_PATH).'/oxid-esales/oxideshop-pe/migration/migrations.yml']);
        $inputEE = array_merge($cliConfigurationValues, ['--configuration' => $configs->getVar(ConfigFile::PARAMETER_VENDOR_PATH).'/oxid-esales/oxideshop-ee/migration/migrations.yml']);
        $inputProject = array_merge($cliConfigurationValues, ['--configuration' => $configs->getVar(ConfigFile::PARAMETER_SOURCE_PATH).'/migration/project_migrations.yml']);

        $output = null;
        $editionSelector = new EditionSelector();

        $this->runMigration($inputCE, $output);
        if ($editionSelector->isEnterprise()) {
            $this->runMigration($inputPE, $output);
            $this->runMigration($inputEE, $output);
        }
        if ($editionSelector->isProfessional()) {
            $this->runMigration($inputPE, $output);
        }

        $this->runMigration($inputProject, $output);

        return $output;
    }

    protected function runMigration($input, $output) {
        $helperSet = new \Symfony\Component\Console\Helper\HelperSet();
        $application = \Doctrine\DBAL\Migrations\Tools\Console\ConsoleRunner::createApplication($helperSet);
        $application->setAutoExit(false);
        if ($input['command'] !== self::MIGRATE_COMMAND || $this->migrationExists($input)) {
            $application->run(new ArrayInput($input), $output);
        }
        return $application;
    }

    protected function migrationExists($input)
    {
        $file = $input['--configuration'];
        $directoryPath = dirname($file);
        $migrationsDir = $directoryPath.DIRECTORY_SEPARATOR.'data';
        if (strpos($file, 'project_migrations')) {
            $migrationsDir = $directoryPath.DIRECTORY_SEPARATOR.'project_data';
        }
        if (count(scandir($migrationsDir)) > 3)
            return true;
        return false;
    }
}
