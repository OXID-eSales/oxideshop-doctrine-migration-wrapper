<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

class VersionTestMigrationCe extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `test_doctrine_migration_wrapper` (`id` CHAR(32) NOT NULL);');
        $this->addSql("INSERT INTO `test_doctrine_migration_wrapper` (`id`) VALUES ('ce_migration');");
    }

    public function down(Schema $schema): void
    {
    }
}
