<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper\Tests\Unit;

use OxidEsales\DoctrineMigrationWrapper\DoctrineApplicationBuilder;
use OxidEsales\DoctrineMigrationWrapper\MigrationArgumentParser;
use OxidEsales\DoctrineMigrationWrapper\MigrationAvailabilityChecker;
use OxidEsales\DoctrineMigrationWrapper\Migrations;
use OxidEsales\DoctrineMigrationWrapper\MigrationsPathProvider;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Application;
use Prophecy\Argument;
use Symfony\Component\Console\Input\ArrayInput;

final class MigrationArgumentParserTest extends TestCase
{
    public function provideArgumentData(): array
    {
        return [
            [
                ['./vendor/bin/oe-eshop-db_migrate', 'migrations:migrate'],
                [
                    'command' => 'migrations:migrate',
                    'edition' => null,
                    'flags' => []
                ]
            ],
            [
                ['./vendor/bin/oe-eshop-db_migrate', 'migrations:migrate', '--dry-run'],
                [
                    'command' => 'migrations:migrate',
                    'edition' => null,
                    'flags' => ['--dry-run' => null]
                ]
            ],
            [
                ['./vendor/bin/oe-eshop-db_migrate', 'migrations:migrate', 'eE', '--dry-run'],
                [
                    'command' => 'migrations:migrate',
                    'edition' => 'eE',
                    'flags' => ['--dry-run' => null]
                ]
            ],
            [
                ['./vendor/bin/oe-eshop-db_migrate', 'migrations:migrate', '--dry-run', '-a=test'],
                [
                    'command' => 'migrations:migrate',
                    'edition' => null,
                    'flags' => ['--dry-run' => null, '-a' => 'test']
                ]
            ],
            [
                [
                    './vendor/bin/oe-eshop-db_migrate',
                    'migrations:migrate',
                    'cE',
                    '--write-sql=/var/www/html/source/migration/project_data/'
                ],
                [
                    'command' => 'migrations:migrate',
                    'edition' => 'cE',
                    'flags' => ['--write-sql' => '/var/www/html/source/migration/project_data/']
                ]
            ],
        ];
    }

    /**
     * @dataProvider provideArgumentData
     */
    public function testArgumentPreparation(array $arguments, array $expected): void
    {
        $argumentParser = new MigrationArgumentParser($arguments);

        $this->assertSame(
            $expected,
            [
                'command' => $argumentParser->getCommand(),
                'edition' => $argumentParser->getEdition(),
                'flags' => $argumentParser->getFlags()
            ]
        );
    }
}
