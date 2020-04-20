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

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper\source\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
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
