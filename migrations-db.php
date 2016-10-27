<?php

$configFileName = realpath(trim(getenv('ESHOP_CONFIG_PATH')));

if (!(file_exists($configFileName) && !is_dir($configFileName))) {
    $items = [
        "Unable to find eShop config.inc.php file.",
        "You can override the path by using ESHOP_CONFIG_PATH environment variable.",
        "\n"
    ];
    $message = implode(" ", $items);
    die($message);
}

$configFile = new \OxidEsales\Eshop\Core\ConfigFile($configFileName);

$connectionParams = array(
    'dbname' => $configFile->dbName,
    'user' => $configFile->dbUser,
    'password' => $configFile->dbPwd,
    'host' => $configFile->dbHost,
    'driver' => $configFile->dbType
);

return $connectionParams;
