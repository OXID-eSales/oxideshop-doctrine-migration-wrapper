<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class MigrationsPathProvider implements MigrationsPathProviderInterface
{
    private BasicContextInterface $context;
    private ShopConfiguration $shopConfiguration;
    private string $defaultFilename;

    public function __construct()
    {
        $this->defaultFilename = 'migrations.yml';
        $this->context = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);
        $this->shopConfiguration = BootstrapContainerFactory::getBootstrapContainer()
            ->get(ProjectConfigurationDaoInterface::class)
            ->getConfiguration()
            ->getShopConfiguration($this->context->getDefaultShopId());
    }

    public function getMigrationsPath($edition = null): array
    {
        $allMigrationPaths = array_merge($this->getShopPaths(), $this->getModulesPath());

        if ($edition === null) {
            return $allMigrationPaths;
        }

        $migrationPaths = [];
        foreach ($allMigrationPaths as $migrationEdition => $migrationPath) {
            if (strtolower($migrationEdition) === strtolower($edition)) {
                $migrationPaths[$migrationEdition] = $migrationPath;
                break;
            }
        }

        return $migrationPaths;
    }

    private function getShopPaths(): array
    {
        $paths = [
            'ce' => $this->getMigrationFilePath($this->context->getSourcePath(), $this->defaultFilename),
        ];
        if (!$this->context->getEdition()->isCommunityEdition()) {
            $paths['pe'] = $this->getMigrationFilePath(
                $this->context->getEditionSourcePath(Edition::Professional),
                $this->defaultFilename
            );
        }
        if ($this->context->getEdition() === Edition::Enterprise) {
            $paths['ee'] = $this->getMigrationFilePath(
                $this->context->getEditionSourcePath(Edition::Enterprise),
                $this->defaultFilename
            );
        }
        $paths['pr'] = $this->getMigrationFilePath(
            $this->context->getSourcePath(),
            'project_migrations.yml'
        );

        return $paths;
    }

    private function getModulesPath(): array
    {
        $paths = [];
        foreach ($this->shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $moduleSource = Path::join(
                $this->context->getShopRootPath(),
                $moduleConfiguration->getModuleSource(),
            );
            $migrationConfigurationPath = $this->getMigrationFilePath($moduleSource, $this->defaultFilename);
            if (file_exists($migrationConfigurationPath)) {
                $paths[$moduleConfiguration->getId()] = $migrationConfigurationPath;
            }
        }

        return $paths;
    }

    private function getMigrationFilePath(string $sourcePath, $filename): string
    {
        return Path::join(
            $sourcePath,
            'migration',
            $filename,
        );
    }
}
