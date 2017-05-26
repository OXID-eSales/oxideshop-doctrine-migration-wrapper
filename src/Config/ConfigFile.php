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

namespace OxidEsales\DoctrineMigrations\Config;

/**
 * Wraps and provides getters for configuration constants stored in configuration file (usually config.inc.php).
 */
class ConfigFile
{
    const PARAMETER_VENDOR_PATH = 'vendor_path';

    const PARAMETER_SOURCE_PATH = 'source_path';

    /**
     * Initializes the instance. Loads config variables from the file.
     *
     * @param string $pathToConfigIncFile Configuration file name.
     *
     * @throws \Exception if config.inc.php does not exist.
     */
    public function __construct($pathToConfigIncFile = null)
    {
        $pathToConfigIncFile = '';
        $rootPath = '';
        $pathToAutoloadFromThisScript = '/';
        $autoloaderName = 'vendor/autoload.php';
        $count = 0;
        while ($count < 8) {
            $fullPathToAutoload = __DIR__ . $pathToAutoloadFromThisScript;
            if (is_file($fullPathToAutoload . $autoloaderName)) {
                $rootPath = $fullPathToAutoload;
                $pathToConfigIncFile = $rootPath.'source/config.inc.php';
                break;
            }
            $pathToAutoloadFromThisScript = '/..' . $pathToAutoloadFromThisScript;
            $count++;
        }
        if (empty($pathToConfigIncFile)) {
            throw new \Exception('File config.inc.php was not found!');
        }

        $this->setVar(static::PARAMETER_VENDOR_PATH, $rootPath.'vendor');
        $this->setVar(static::PARAMETER_SOURCE_PATH, $rootPath.'source');
        $this->loadVars($pathToConfigIncFile);
    }

    /**
     * Returns loaded variable value by name.
     *
     * @param string $varName Variable name
     *
     * @return mixed
     */
    public function getVar($varName)
    {
        return isset($this->$varName) ? $this->$varName : null;
    }

    /**
     * Set config variable.
     *
     * @param string $varName Variable name
     * @param string $value   Variable value
     */
    public function setVar($varName, $value)
    {
        $this->$varName = $value;
    }

    /**
     * Checks by name if variable is set
     *
     * @param string $varName Variable name
     *
     * @return bool
     */
    public function isVarSet($varName)
    {
        return isset($this->$varName);
    }

    /**
     * Returns all loaded vars as an array
     *
     * @return array[string]mixed
     */
    public function getVars()
    {
        return get_object_vars($this);
    }

    /**
     * Performs variable loading from configuration file by including the php file.
     * It works with current configuration file format well,
     * however in case the variable storage format is not satisfactory
     * this method is a subject to be changed.
     *
     * @param string $fileName Configuration file name
     */
    protected function loadVars($fileName)
    {
        include $fileName;
    }
}
