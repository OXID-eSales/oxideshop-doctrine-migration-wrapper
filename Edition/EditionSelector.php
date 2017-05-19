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

namespace OxidEsales\DoctrineMigrations\Edition;

use OxidEsales\DoctrineMigrations\Config\ConfigFile;

/**
 * Class is responsible for returning edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class EditionSelector
{
    const ENTERPRISE = 'EE';

    const PROFESSIONAL = 'PE';

    const COMMUNITY = 'CE';

    /** @var string Edition abbreviation  */
    private $edition = null;

    /**
     * EditionSelector constructor.
     *
     * @param string|null                           $edition                 to force edition.
     */
    public function __construct($edition = null, $virtualClassMapProvider = null)
    {
        $this->edition = $edition ?: $this->findEdition();
    }

    /**
     * Method returns edition.
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * @return bool
     */
    public function isEnterprise()
    {
        return $this->getEdition() === static::ENTERPRISE;
    }

    /**
     * @return bool
     */
    public function isProfessional()
    {
        return $this->getEdition() === static::PROFESSIONAL;
    }

    /**
     * @return bool
     */
    public function isCommunity()
    {
        return $this->getEdition() === static::COMMUNITY;
    }

    /**
     * Check for forced edition in config file. If edition is not specified,
     * determine it by ClassMap existence.
     *
     * @return string
     */
    protected function findEdition()
    {
        $configFile = new ConfigFile();
        $edition = $configFile->getVar('edition') ?: $this->findEditionByClassMap();
        $configFile->setVar('edition', $edition);

        return strtoupper($edition);
    }

    protected function findEditionByClassMap()
    {
        $edition = static::COMMUNITY;
        if (class_exists(\OxidEsales\EshopEnterprise\Core\Autoload\VirtualNameSpaceClassMap::class)) {
            $edition = static::ENTERPRISE;
        } elseif (class_exists(\OxidEsales\EshopProfessional\Core\Autoload\VirtualNameSpaceClassMap::class)) {
            $edition = static::PROFESSIONAL;
        }

        return $edition;
    }
}
