<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;

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

(new DotenvLoader((new ProjectRootLocator())->getProjectRoot()))->loadEnvironmentVariables();

$migrationsBuilder = new \OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder();
$migrations = $migrationsBuilder->build();

$argumentParser = new MigrationArgumentParser($argv);
exit($migrations->execute($argumentParser->getCommand(), $argumentParser->getEdition(), $argumentParser->getFlags()));
