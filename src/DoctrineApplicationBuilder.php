<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\DoctrineMigrationWrapper;

use Doctrine\Migrations\Tools\Console\ConsoleRunner;
use Symfony\Component\Console\Application;

class DoctrineApplicationBuilder
{
    /**
     * Return new application for each build.
     * Application has a reference to command which has internal cache.
     * Reusing same application object with same command leads to an errors due to an old configuration.
     * For example first run with a CE migrations
     * second run with PE migrations
     * both runs would take path to CE migrations.
     *
     * @return Application
     */
    public function build()
    {
        $helperSet = new \Symfony\Component\Console\Helper\HelperSet();
        $doctrineApplication = ConsoleRunner::createApplication($helperSet);
        $doctrineApplication->setAutoExit(false);
        $doctrineApplication->setCatchExceptions(false); // we handle the exception on our own!

        return $doctrineApplication;
    }
}