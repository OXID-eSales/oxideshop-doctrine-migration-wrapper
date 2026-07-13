<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Env\DotenvLoader;
use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ProjectRootLocator;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\TaggedMigrationExecutor;
use Symfony\Component\Filesystem\Path;

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

$projectRoot = (new ProjectRootLocator())->getProjectRoot();
(new DotenvLoader($projectRoot))->loadEnvironmentVariables();

$migrationsBuilder = new \OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder();
$migrations = $migrationsBuilder->build();

$argumentParser = new MigrationArgumentParser($argv);
$exitCode = $migrations->execute($argumentParser->getCommand(), $argumentParser->getEdition(), $argumentParser->getFlags());

if (
    $exitCode === 0
    && $argumentParser->getCommand() === Migrations::MIGRATE_COMMAND
    && $argumentParser->getEdition() === null
) {
    require_once Path::join($projectRoot, 'source', 'bootstrap.php');
    $exitCode = ContainerFacade::get(TaggedMigrationExecutor::class)
        ->executeWithOptions($argumentParser->getFlags());
}

exit($exitCode);
