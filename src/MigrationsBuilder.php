<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use Symfony\Component\Filesystem\Path;

class MigrationsBuilder
{
    public function build(): Migrations
    {
        return new Migrations(
            new DoctrineApplicationBuilder(),
            Path::join(__DIR__, 'migrations-db.php'),
            new MigrationAvailabilityChecker(),
            new MigrationsPathProvider()
        );
    }
}
