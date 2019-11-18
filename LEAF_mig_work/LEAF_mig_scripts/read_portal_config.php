<?php

// php read_portal_config <path_to_portal_directory>

if (count($argv) < 2) {
    return "{}";
}

$portalDirectory = $argv[1];

include $portalDirectory . '/db_config.php';

$cfg = new \DB_Config;
$cfg2 = new \Config;

$res = serialize([
    'dbName' => $cfg->dbName,
    'orgchartPath' => isset(\Config::$orgchartPath) ? realpath($portalDirectory . \Config::$orgchartPath) : null,
    'phonedbName' => $cfg2->phonedbName,
]);
echo str_replace(array("\n", "\r"), '', $res);