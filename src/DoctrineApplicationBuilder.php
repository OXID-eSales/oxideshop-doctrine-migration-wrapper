<?php
/**
 * Created by PhpStorm.
 * User: saulius stasiukaitis
 * Date: 5/24/2017
 * Time: 2:23 PM
 */

namespace OxidEsales\DoctrineMigrationWrapper;

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
     * @return \Symfony\Component\Console\Application
     */
    public function build()
    {
        $helperSet = new \Symfony\Component\Console\Helper\HelperSet();
        $doctrineApplication = \Doctrine\DBAL\Migrations\Tools\Console\ConsoleRunner::createApplication($helperSet);
        $doctrineApplication->setAutoExit(false);
        $doctrineApplication->setCatchExceptions(false); // we handle the exception on our own!

        return $doctrineApplication;
    }
}
