<?php

// php read_orgchart_config <path_to_orgchart_directory>

if (count($argv) < 2) {
    return "{}";
}

$orgchartDirectory = $argv[1];

include $orgchartDirectory . '/config.php';

$cfg = new \Orgchart\Config;
$res = serialize([
    'dbName' => $cfg->dbName
]);

echo str_replace(array("\n", "\r"), '', $res);

