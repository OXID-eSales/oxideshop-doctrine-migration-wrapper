<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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

    public function testReturnFalseWhenMigrationsDirectoryDoesNotExist(): void
    {
        vfsStream::setup('root', 777, ['migration' => ['migrations.yml' => 'configuration for migrations']]);
        $pathToMigrationConfigurationFile = vfsStream::url('root/migration/migrations.yml');

        $availabilityChecker = new MigrationAvailabilityChecker();
        $this->assertFalse($availabilityChecker->migrationExists($pathToMigrationConfigurationFile));
    }

    public function testEnsureMigrationsDirectoryExistsCreatesMissingDirectories(): void
    {
        $structure = [
            'migration' => [
                'migrations.yml' => 'configuration for migrations',
                'project_migrations.yml' => 'configuration for migrations  - project',
            ]
        ];

        vfsStream::setup('root', 777, $structure);

        $availabilityChecker = new MigrationAvailabilityChecker();
        $availabilityChecker->ensureMigrationsDirectoryExists(vfsStream::url('root/migration/migrations.yml'));
        $availabilityChecker->ensureMigrationsDirectoryExists(vfsStream::url('root/migration/project_migrations.yml'));

        $this->assertDirectoryExists(vfsStream::url('root/migration/data'));
        $this->assertDirectoryExists(vfsStream::url('root/migration/project_data'));
    }

    public function testEnsureMigrationsDirectoryExistsSkipsMissingConfigurationFile(): void
    {
        vfsStream::setup('root', 777, ['migration' => []]);

        (new MigrationAvailabilityChecker())
            ->ensureMigrationsDirectoryExists(vfsStream::url('root/migration/migrations.yml'));

        $this->assertDirectoryDoesNotExist(vfsStream::url('root/migration/data'));
    }
}
