<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\MigrationsProject;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class VersionTestMigrationProject extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO `test_doctrine_migration_wrapper` (`id`) VALUES ('project_migration');");
    }

    public function down(Schema $schema): void
    {
    }
}
