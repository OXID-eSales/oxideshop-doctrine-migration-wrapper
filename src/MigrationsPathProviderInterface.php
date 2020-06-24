<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

Interface MigrationsPathProviderInterface
{

    /**
     * @param null $edition
     *
     * @return array
     */
    public function getMigrationsPath($edition = null): array;
}
