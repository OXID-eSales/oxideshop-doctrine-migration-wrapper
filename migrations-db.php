<?php

$bootstrapFileName = realpath(trim(getenv('ESHOP_BOOTSTRAP_PATH')));

if (!(file_exists($bootstrapFileName) && !is_dir($bootstrapFileName))) {
    $items = [
        "Unable to find eShop bootstrap.php file.",
        "You can override the path by using ESHOP_BOOTSTRAP_PATH environment variable.",
        "\n"
    ];
    $message = implode(" ", $items);
    die($message);
}

require_once($bootstrapFileName);

$configFile = new \OxidEsales\Eshop\Core\ConfigFile(OX_BASE_PATH . "config.inc.php");

$connectionParams = array(
    'dbname' => $configFile->dbName,
    'user' => $configFile->dbUser,
    'password' => $configFile->dbPwd,
    'host' => $configFile->dbHost,
    'driver' => $configFile->dbType
);

return $connectionParams;
