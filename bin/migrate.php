<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

$autoloadFileExist = false;
$autoloadFiles = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
    __DIR__ . '/../../../../vendor/autoload.php',
];

foreach ($autoloadFiles as $autoloadFile) {
    if (file_exists($autoloadFile)) {
        require_once $autoloadFile;
        $autoloadFileExist = true;
        break;
    }
}

if (!$autoloadFileExist) {
    exit('Autoload file was not found!');
}

$migrationsBuilder = new \OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder();
$migrations = $migrationsBuilder->build();

$command = $argv[1] ?? null;
$edition = $argv[2] ?? null;
$flags = [];

if (isset($argv[3])) {

    // Do not alter $argv itself
    $copyOfArgv = $argv;

    unset(
        $copyOfArgv[0],
        $copyOfArgv[1],
        $copyOfArgv[2]
    );

    foreach ($copyOfArgv as $flag) {

        /*
         * Determines if a param has also a value
         * if case  : --write-sql=/var/www/html/source/migration/project_data/
         * else case: --dry-run
         */
        $keyValuePair = preg_split('/=/', $flag);

        if (count($keyValuePair) === 2) {
            $flags[$keyValuePair[0]] = $keyValuePair[1];
        } else {
            $flags[$flag] = null;
        }
    }
}

exit($migrations->execute($command, $edition, $flags));
