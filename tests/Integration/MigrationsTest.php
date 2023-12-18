<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper\Tests\Integration;

use OxidEsales\DoctrineMigrationWrapper\MigrationsBuilder;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Exception\CommandNotFoundException;

final class MigrationsTest extends TestCase
{
    private string $ceMigrationClass = 'VersionTestMigrationCe';
    private string $projectMigrationClass = 'VersionTestMigrationProject';
    private string $tableCreatedByCeMigration = 'test_doctrine_migration_wrapper';
    private string $entryAddedByCeMigration = 'ce_migration';
    private string $entryAddedByProjectMigration = 'project_migration';

    public function testExecuteWithUnknownCommandWillOutputAnError(): void
    {
        $this->expectException(CommandNotFoundException::class);

        $migration = (new MigrationsBuilder())->build();

        $migration->execute(uniqid('command-', true));
    }

    public function testExecuteWithEmptyCommandWillNotOutputAnError(): void
    {
        $migration = (new MigrationsBuilder())->build();

        $result = $migration->execute('');

        $this->assertEquals(0, $result);
    }

    public function testMigrateSuccess(): void
    {
        $db = DatabaseProvider::getDb();
        $this->copyMigrationFixturesToShop();
        $migration = (new MigrationsBuilder())->build();

        $migration->execute('migrations:migrate');

        $totalEntriesCount = $db->select(
            "SELECT * FROM `$this->tableCreatedByCeMigration`"
        )
            ->count();
        $ceEntriesCount = $db->select(
            "SELECT * FROM `$this->tableCreatedByCeMigration` WHERE `id` = '$this->entryAddedByCeMigration'"
        )
            ->count();
        $projectEntriesCount = $db->select(
            "SELECT * FROM `$this->tableCreatedByCeMigration` WHERE `id` = '$this->entryAddedByProjectMigration'"
        )
            ->count();
        $this->assertEquals(2, $totalEntriesCount);
        $this->assertEquals(1, $ceEntriesCount);
        $this->assertEquals(1, $projectEntriesCount);

        $this->removeMigrationFixturesFromShop();
        $this->undoMigrationChangesInDatabase();
    }

    public function testExecuteWithMigrationsGenerateWillAddSuiteInfoToOutput(): void
    {
        $suiteCode = 'CE';
        $migration = (new MigrationsBuilder())->build();
        $output = new ConsoleOutputSpy();

        $migration->setOutput($output);

        $migration->execute('migrations:generate', $suiteCode);

        $this->assertStringContainsString($suiteCode, $output->getWriteLnContents());
    }

    private function copyMigrationFixturesToShop(): void
    {
        $shopSource = (new Facts())->getSourcePath();
        copy(
            __DIR__ . "/Fixtures/migration/data/$this->ceMigrationClass.php",
            "$shopSource/migration/data/$this->ceMigrationClass.php"
        );
        copy(
            __DIR__ . "/Fixtures/migration/project_data/$this->projectMigrationClass.php",
            "$shopSource/migration/project_data/$this->projectMigrationClass.php"
        );
    }

    private function removeMigrationFixturesFromShop(): void
    {
        $shopSource = (new Facts())->getSourcePath();
        unlink("$shopSource/migration/data/$this->ceMigrationClass.php");
        unlink("$shopSource/migration/project_data/$this->projectMigrationClass.php");
    }

    private function undoMigrationChangesInDatabase(): void
    {
        DatabaseProvider::getDb()->execute("DROP TABLE `$this->tableCreatedByCeMigration`");
        DatabaseProvider::getDb()->execute("DELETE FROM `oxmigrations_ce` WHERE `version` LIKE '%$this->ceMigrationClass'");
        DatabaseProvider::getDb()->execute("DELETE FROM `oxmigrations_project` WHERE `version` LIKE '%$this->projectMigrationClass'");
    }
}
