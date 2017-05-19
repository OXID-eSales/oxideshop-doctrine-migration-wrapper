<?php

use OxidEsales\DoctrineMigrations\Config\ConfigFile;

$configFile = new ConfigFile();

return [
    'dbname' => $configFile->dbName,
    'user' => $configFile->dbUser,
    'password' => $configFile->dbPwd,
    'host' => $configFile->dbHost,
    'driver' => $configFile->dbType
];
