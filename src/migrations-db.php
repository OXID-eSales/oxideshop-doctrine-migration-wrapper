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

$facts = new \OxidEsales\Facts\Facts();

return [
    'dbname' => $facts->getDatabaseName(),
    'user' => $facts->getDatabaseUserName(),
    'password' => $facts->getDatabasePassword(),
    'host' => $facts->getDatabaseHost(),
    'port' => $facts->getDatabasePort(),
    'driver' => $facts->getDatabaseDriver(),
    'charset' => 'utf8',
    'driverOptions' => [
        \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET @@SESSION.sql_mode=\'\''
    ]
];
