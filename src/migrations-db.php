<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use OxidEsales\Facts\Facts;

$facts = new Facts();

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
