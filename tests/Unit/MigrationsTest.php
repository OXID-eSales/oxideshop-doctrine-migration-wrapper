<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper\Tests\Unit;

use Doctrine\Migrations\Exception\MigrationClassNotFound;
use OxidEsales\DoctrineMigrationWrapper\DoctrineApplicationBuilder;
use OxidEsales\DoctrineMigrationWrapper\MigrationAvailabilityChecker;
use OxidEsales\DoctrineMigrationWrapper\Migrations;
use OxidEsales\DoctrineMigrationWrapper\MigrationsPathProvider;
use OxidEsales\Facts\Facts;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;

final class MigrationsTest extends TestCase
{
    use ProphecyTrait;

    public function testCallsDoctrineMigrations(): void
    {
        $doctrineApplication = $this->getDoctrineMock(true);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub(['edition' => 'path_to_migrations']);

        $pathToDbConfig = '';

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $pathToDbConfig,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );
        $this->assertSame(0, $migrations->execute('migrations:migrate'));
    }

    public function testExecuteCEMigration(): void
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';

        $input = new ArrayInput([
            '--configuration' => $ceMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $doctrineApplication = $this->getDoctrineMock(true, $input);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub(['ce' => $ceMigrationsPath]);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $migrations->execute($command);
    }

    public function testExecuteAllMigrations(): void
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';
        $peMigrationsPath = 'path_to_pe_migrations';
        $eeMigrationsPath = 'path_to_ee_migrations';
        $migrationPaths = [
            'ce' => $ceMigrationsPath,
            'pe' => $peMigrationsPath,
            'ee' => $eeMigrationsPath,
        ];

        $inputCE = new ArrayInput([
            '--configuration' => $ceMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $inputPE = new ArrayInput([
            '--configuration' => $peMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $inputEE = new ArrayInput([
            '--configuration' => $eeMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $doctrineApplication = $this->createPartialMock(Application::class, ['run', 'get']);

        $doctrineApplication
            ->expects($this->exactly(3))
            ->method('run')
            ->with(
                $this->callback(function ($argument) use ($inputCE, $inputPE, $inputEE) {
                    static $count = 0;
                    $count++;
                    return match ($count) {
                        1 => $argument == $inputCE,
                        2 => $argument == $inputPE,
                        3 => $argument == $inputEE,
                        default => false,
                    };
                })
            );

        $doctrineApplication->method('get')
            ->willReturn($this->createMock(Command::class));

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub($migrationPaths);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $migrations->execute($command);
    }

    public function testExecuteOnlyRequestedMigration(): void
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $eeMigrationsPath = 'path_to_ee_migrations';
        $migrationPaths = [
            'eE' => $eeMigrationsPath,
        ];

        $inputEE = new ArrayInput([
            '--configuration' => $eeMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command
        ]);

        $doctrineApplication = $this->createPartialMock(Application::class, ['run', 'get']);
        $doctrineApplication->expects($this->once())->method('run')->with($inputEE);
        $doctrineApplication->method('get')
            ->willReturn($this->createMock(Command::class));

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub($migrationPaths);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $migrations->execute($command, 'Ee');
    }

    public function testNoErrorWhenNoMigrationExistForRequestedEdition(): void
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $migrationPaths = [
            'ce' => 'path_to_ce_migrations',
            'pe' => 'path_to_pe_migrations',
            'ee' => 'path_to_ee_migrations',
        ];

        $doctrineApplication = $this->createPartialMock(Application::class, ['run']);
        $doctrineApplication->expects($this->never())->method('run');

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub($migrationPaths);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(false);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $migrations->execute($command, 'PR');
    }

    public function testSkipMigrationWhenItDoesNotExist(): void
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';

        $doctrineApplication = $this->getDoctrineMock(false);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub(['ce' => $ceMigrationsPath]);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(false);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $migrations->execute($command);
    }

    public function testMigrationAvailabilityCheckerCalledWithCorrectPath(): void
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';

        $doctrineApplication = $this->getDoctrineStub();

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub(['ce' => $ceMigrationsPath]);

        $migrationAvailabilityChecker = $this->createPartialMock(
            MigrationAvailabilityChecker::class,
            ['migrationExists']
        );
        $migrationAvailabilityChecker->expects($this->atLeastOnce())
            ->method('migrationExists')
            ->with($ceMigrationsPath);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $migrations->execute($command);
    }

    public function testRunGenerateMigrationCommandEvenIfNoMigrationExist(): void
    {
        $command = 'migrations:generate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $ceMigrationsPath = 'path_to_ce_migrations';

        $doctrineApplication = $this->getDoctrineMock(true);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub(['ce' => $ceMigrationsPath]);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(false);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $migrations->execute($command);
    }

    public function testReturnErrorCodeWhenMigrationFail(): void
    {
        $errorCode = 1;

        $doctrineApplication = $this->getDoctrineStub($errorCode);

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub(['edition' => 'path_to_migrations']);

        $pathToDbConfig = '';

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $pathToDbConfig,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $this->assertSame($errorCode, $migrations->execute('migrations:migrate'));
    }

    public function testExecuteWithEmptyInputWillCallDefaultCommand(): void
    {
        $application = $this->prophesize(Application::class);
        $consoleCommand = $this->prophesize(Command::class);
        $consoleCommand->getName()->willReturn('command-name');
        $consoleCommand->setName(Argument::any())->willReturn($consoleCommand);
        $application->get(Argument::any())->willReturn($consoleCommand);
        $application->run(Argument::type(ArrayInput::class), Argument::any())->willReturn(0);
        $applicationBuilder = $this->prophesize(DoctrineApplicationBuilder::class);
        $applicationBuilder->build()->willReturn($application);
        $migrationsPathProvider = $this->prophesize(MigrationsPathProvider::class);
        $migrationsPathProvider->getMigrationsPath(null)->willReturn(['suite' => 'something-path']);
        $checker = $this->prophesize(MigrationAvailabilityChecker::class);

        (new Migrations(
            $applicationBuilder->reveal(),
            '',
            $checker->reveal(),
            $migrationsPathProvider->reveal()
        ))->execute('');

        $application->run(
            Argument::that(
                static function (ArrayInput $input) {
                    return $input->getParameterOption('command') === 'migrations:status';
                }
            ),
            Argument::any()
        )->shouldBeCalledOnce();
    }

    public function testExecuteMigrationWithFlags(): void
    {
        $command = 'migrations:migrate';
        $flags = ['--a-new-flag', '--another-new-flag'];
        $dbConfigFilePath = 'path_to_DB_config_file';
        $eeMigrationsPath = 'path_to_ee_migrations';
        $migrationPaths = [
            'eE' => $eeMigrationsPath,
        ];

        $inputEE = [
            '--configuration' => $eeMigrationsPath,
            '--db-configuration' => $dbConfigFilePath,
            '-n' => true,
            'command' => $command,
        ];
        $inputEE = new ArrayInput(array_merge($inputEE, $flags));

        $doctrineApplication = $this->createPartialMock(Application::class, ['run', 'get']);
        $doctrineApplication->expects($this->once())->method('run')->with($inputEE);
        $doctrineApplication->method('get')
            ->willReturn($this->createMock(Command::class));

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub($migrationPaths);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $migrations->execute($command, 'Ee', $flags);
    }

    /**
     * @dataProvider badFlagsDataProvider
     */
    public function testRaiseErrorExecuteMigrationWithInvalidNFlag($message, $flags): void
    {
        $command = 'migrations:migrate';
        $dbConfigFilePath = 'path_to_DB_config_file';
        $eeMigrationsPath = 'path_to_ee_migrations';
        $migrationPaths = [
            'eE' => $eeMigrationsPath,
        ];

        $doctrineApplication = $this->createPartialMock(Application::class, ['run']);
        $doctrineApplication->expects($this->never())->method('run');

        $doctrineApplicationBuilder = $this->getDoctrineApplicationBuilderStub($doctrineApplication);

        $migrationsPathProvider = $this->getMigrationsPathProviderStub($migrationPaths);

        $migrationAvailabilityChecker = $this->getMigrationAvailabilityStub(true);

        $migrations = new Migrations(
            $doctrineApplicationBuilder,
            $dbConfigFilePath,
            $migrationAvailabilityChecker,
            $migrationsPathProvider
        );

        $this->expectException(\Symfony\Component\Console\Exception\InvalidOptionException::class);
        $this->expectExceptionMessage($message);

        $migrations->execute($command, 'Ee', $flags);
    }

    public function testExecuteWithMissingMigrationClassWillRethrowMentioningSuiteInExceptionMessage(): void
    {
        $command = 'migrations:execute ABC';
        $suite = 'some-edition';
        $suiteFormatted = strtoupper($suite);
        $originalExceptionMessage = 'Some exception message.';
        $application = $this->prophesize(Application::class);
        $consoleCommand = $this->prophesize(Command::class);
        $consoleCommand->getName()->willReturn($command);
        $consoleCommand->setName("$command $suiteFormatted")->willReturn($consoleCommand);
        $application->get($command)->willReturn($consoleCommand);
        $application->run(Argument::type(ArrayInput::class), Argument::any())
            ->willThrow(
                new MigrationClassNotFound($originalExceptionMessage)
            );
        $applicationBuilder = $this->prophesize(DoctrineApplicationBuilder::class);
        $applicationBuilder->build()->willReturn($application);
        $migrationsPathProvider = $this->prophesize(MigrationsPathProvider::class);
        $migrationsPathProvider->getMigrationsPath(null)->willReturn(
            [$suite => 'some-path-to-migration-config-file']
        );
        $checker = $this->prophesize(MigrationAvailabilityChecker::class);

        $this->expectException(MigrationClassNotFound::class);
        $this->expectExceptionMessageMatches("/$suite/");
        $this->expectExceptionMessageMatches("/$originalExceptionMessage/");

        (new Migrations(
            $applicationBuilder->reveal(),
            'some-path-to-db-config-file',
            $checker->reveal(),
            $migrationsPathProvider->reveal()
        ))
            ->execute($command);
    }

    public static function badFlagsDataProvider(): array
    {
        return [
            [
                'message' => 'The following flags are not allowed to be overwritten: --db-configuration',
                'flags' => ['--db-configuration' => 'path_to_DB_config_file']
            ],
            [
                'message' => 'The following flags are not allowed to be overwritten: -n',
                'flags' => ['-n' => null]
            ]
        ];
    }


    private function getDoctrineMock($runsAtLeastOnce, $callWith = null): MockObject
    {
        $doctrineApplication = $this->createPartialMock(Application::class, ['run', 'get']);

        if (!$runsAtLeastOnce) {
            $doctrineApplication->expects($this->never())->method('run');
            return $doctrineApplication;
        }
        $doctrineApplication->method('get')
            ->willReturn($this->createMock(Command::class));
        if ($callWith) {
            $doctrineApplication->expects($this->atLeastOnce())->method('run')->with($callWith);
        } else {
            $doctrineApplication->expects($this->atLeastOnce())->method('run');
        }

        return $doctrineApplication;
    }

    private function getDoctrineStub($result = null): MockObject
    {
        $doctrineApplication = $this->createPartialMock(Application::class, ['run', 'get']);
        $doctrineApplication->method('run')->willReturn($result ? 1 : 0);
        $doctrineApplication->method('get')
            ->willReturn($this->createMock(Command::class));

        return $doctrineApplication;
    }

    private function getDoctrineApplicationBuilderStub($doctrineApplication): MockObject
    {
        $doctrineApplicationBuilder = $this->createPartialMock(DoctrineApplicationBuilder::class, ['build']);
        $doctrineApplicationBuilder->method('build')->willReturn($doctrineApplication);

        return $doctrineApplicationBuilder;
    }

    private function getMigrationsPathProviderStub($migrationPaths): MockObject
    {
        $migrationsPathProvider = $this->getMockBuilder(MigrationsPathProvider::class)
            ->onlyMethods(['getMigrationsPath'])
            ->setConstructorArgs([new Facts()])
            ->getMock();

        $migrationsPathProvider->method('getMigrationsPath')->willReturn($migrationPaths);

        return $migrationsPathProvider;
    }

    private function getMigrationAvailabilityStub($ifMigrationsAvailable): MockObject
    {
        $migrationAvailabilityChecker = $this->createPartialMock(
            MigrationAvailabilityChecker::class,
            ['migrationExists']
        );
        $migrationAvailabilityChecker->method('migrationExists')->willReturn($ifMigrationsAvailable);

        return $migrationAvailabilityChecker;
    }
}
