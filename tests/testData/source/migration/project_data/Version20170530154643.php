<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper\source\Migrations;

use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Test migration to create data which could be used to check if Migrations actually works.
 */
class Version20170530154643 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO `test_doctrine_migration_wrapper` (`id`) VALUES ('project_migration');");
    }

    public function down(Schema $schema): void
    {
    }
}
