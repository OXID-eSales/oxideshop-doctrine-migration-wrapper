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

namespace OxidEsales\DoctrineMigrationWrapper\Tests\Unit;

use org\bovigo\vfs\vfsStream;
use OxidEsales\DoctrineMigrationWrapper\MigrationAvailabilityChecker;
use PHPUnit\Framework\TestCase;

final class MigrationAvailabilityCheckerTest extends TestCase
{
    public function testReturnFalseWhenFileDoesNotExist(): void
    {
        $availabilityChecker = new MigrationAvailabilityChecker();
        $this->assertFalse($availabilityChecker->migrationExists('some_not_existing_file'));
    }

    public function testReturnTrueWhenMigrationExist(): void
    {
        $structure = [
            'migration' => [
                'migrations.yml' => 'configuration for migrations',
                'project_migrations.yml' => 'configuration for migrations  - project',
                'data' => [
                    'Version20170522094119.php' => 'migrations'
                ]
            ]
        ];

        vfsStream::setup('root', 777, $structure);
        $pathToMigrationConfigurationFile = vfsStream::url('root/migration/migrations.yml');

        $availabilityChecker = new MigrationAvailabilityChecker();
        $this->assertTrue($availabilityChecker->migrationExists($pathToMigrationConfigurationFile));
    }

    public function testReturnFalseWhenNoMigrationsExist(): void
    {
        $structure = [
            'migration' => [
                'migrations.yml' => 'configuration for migrations',
                'project_migrations.yml' => 'configuration for migrations  - project',
                'data' => []
            ]
        ];

        vfsStream::setup('root', 777, $structure);
        $pathToMigrationConfigurationFile = vfsStream::url('root/migration/migrations.yml');

        $availabilityChecker = new MigrationAvailabilityChecker();
        $this->assertFalse($availabilityChecker->migrationExists($pathToMigrationConfigurationFile));
    }

    public function testReturnFalseWhenGitKeepExist(): void
    {
        $structure = [
            'migration' => [
                'migrations.yml' => 'configuration for migrations',
                'project_migrations.yml' => 'configuration for migrations  - project',
                'data' => [
                    '.gitkeep' => ''
                ]
            ]
        ];

        vfsStream::setup('root', 777, $structure);
        $pathToMigrationConfigurationFile = vfsStream::url('root/migration/migrations.yml');

        $availabilityChecker = new MigrationAvailabilityChecker();
        $this->assertFalse($availabilityChecker->migrationExists($pathToMigrationConfigurationFile));
    }
}
