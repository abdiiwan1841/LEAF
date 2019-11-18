<?php
$phpPath = "php";
$scriptsPath = "C:\Users\jerem\Documents\GitHub\LEAF\LEAF_mig_work\LEAF_mig_scripts";
$sitesPath = "C:\Users\jerem\Documents\GitHub\LEAF\LEAF_mig_work\LEAF_SITES";
$outputPath = "C:\Users\jerem\Documents\GitHub\LEAF\LEAF_mig_work\siteStructure.json";

$string = file_get_contents($outputPath);

$array = json_decode($string, true);

foreach ($array["portals"] as $portalDB => $portal) 
{
    echo $portalDB . "--".$portal['orgchartDB']."\n";
}