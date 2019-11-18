<?php

class Site
{
    var $site_path;
    var $site_type;
    var $database;
    var $orgchart_path = null;
    var $orgchart_database = null;
}

class SiteHelper
{
    var $phpPath;
    var $scriptsDir;

    function __construct($phpPath, $scriptsDir) {
        $this->phpPath = $phpPath;
        $this->scriptsDir = $scriptsDir;
    }

    public function isPortal($directory)
    {
        return file_exists($directory . '/db_config.php')
            && file_exists($directory . '/db_mysql.php');
    }

    public function isNexus($directory)
    {
        return file_exists($directory . '/config.php')
            && file_exists($directory . '/db_mysql.php');
    }

    //pass in array of sites
    //get res that's an array of orgcharts each with it's portals
    public function organizeSites($sites)
    {
        $res = [];
        $orgcharts = [];
        $orgchartReverse = [];
        $portals = [];

        foreach ($sites as $site)
        {
            if ($site->site_type === 'portal')
            {
                $portals[] = $site;
            }
            else if ($site->site_type === 'orgchart')
            {
                $orgcharts[] = $site;
            }
        }
        //add orgcharts to res
        foreach ($orgcharts as $orgchart)
        {
            
            if (!array_key_exists($orgchart->database, $res))
            {
                $res[$orgchart->database] = [];
                $res[$orgchart->database]['name'] = $orgchart->database;
                $res[$orgchart->database]['orgcharts'] = [];
                $res[$orgchart->database]['portals'] = [];
            }

            $orgchartReverse[$orgchart->site_path] = $orgchart->database;
            $res[$orgchart->database]['orgcharts'][] = $orgchart;
        }
        //add portal to it's orgchart in res
        foreach ($portals as $portal)
        {
            if (array_key_exists($portal->orgchart_path, $orgchartReverse))
            {
                $portalOrg = $orgchartReverse[$portal->orgchart_path];
                $res[$portalOrg]['portals'][] = $portal;
            }
        }

        return $res;
    }

    //pass the directory, return sites
    public function parseSiteDirectory($directory, $orgchartLogger = null, $portalLogger = null, $errorLogger = null)
    {
        $sites = [];
        try
        {
            $dir = new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS | FilesystemIterator::UNIX_PATHS);
            $filter = new RecursiveCallbackFilterIterator($dir, function ($current, $key, $iterator) {
                if ($current->isDir())
                {
                    // skip dotdirectories
                    if (substr($current->getFilename()[0], 0, 1) === '.')
                    {
                        return false;
                    }

                    return true;
                }

                // if orgchart
                if (strpos($current->getFilename(), 'config.php') === 0)
                {
                    return true;
                }

                // if portal
                if (strpos($current->getFilename(), 'db_config.php') === 0)
                {
                    return true;
                }

                return false;
            });

            $iterator = new RecursiveIteratorIterator($filter);

            foreach ($iterator as $info)
            {
                $dir = str_replace($info->getFilename(), '', $info->getPathname());

                $sitePath = str_replace($directory, '', $info->getPath());
                $existingSite = array_key_exists($sitePath, $sites) ? $sites[$sitePath] : null;//get site object by path
                if ($existingSite == null || !isset($existingSite->site_path) || $existingSite->site_path != $sitePath)
                {
                    if ($this->isNexus($dir))
                    {
                        if ($orgchartLogger != null)
                        {
                            $orgchartLogger('Processing ORGCHART: ' . $dir);
                        }

                        $site = new Site;
                        $site->site_path = str_replace($directory, '', $info->getPath());
                        $site->site_type = 'orgchart';

                        $cfg = $this->readNexusConfig($this->convertToUnix($dir));
                        //var_dump($cfg);
                        $site->database = $cfg['dbName'];

                        $sites[$sitePath] = $site;
                    }
                    else
                    {
                        if ($this->isPortal($dir))
                        {
                            if ($portalLogger != null)
                            {
                                $portalLogger('Processing PORTAL: ' . $dir);
                            }
                            $site = new Site;
                            $site->site_path = str_replace($directory, '', $info->getPath());
                            // $site->site_path = $info->getPathname();
                            $site->site_type = 'portal';

                            $cfg = $this->readPortalConfig($this->convertToUnix($dir));
                            //var_dump($cfg);
                            $site->database = $cfg['dbName'];

                            // $site->orgchart_path = $cfg['orgchartPath'];
                            if (isset($cfg['orgchartPath']))
                            {
                                $site->orgchart_path = $this->convertToUnix(
                                    str_replace(
                                        $directory,
                                        '',
                                        $cfg['orgchartPath']
                                    )
                                );

                                if ($portalLogger != null)
                                {
                                    $portalLogger('Reading nexus config');
                                }
                                $orgCfg = $this->readNexusConfig($cfg['orgchartPath']);
                                $site->orgchart_database = $orgCfg['dbName'];
                            }
                            else
                            {
                                if ($portalLogger != null)
                                {
                                    $portalLogger('Reading phonedbName');
                                }
                                $site->orgchart_path = '';
                                $site->orgchart_database = $cfg['phonedbName'];
                            }

                            try {

                                if ($portalLogger != null)
                                {
                                    $portalLogger('Saving it..');
                                }

                                $sites[$sitePath] = $site;
                            } catch (\Exception $ee)
                            {
                                if ($portalLogger != null)
                                {
                                    $portalLogger('ERROR: ' . $ee->getMessage());
                                }
                            }
                        }
                    }

                }
            }
            // return $files;
            return $sites;
        }
        catch (\Exception $e)
        {

            if ($errorLogger != null) {
                $errorLogger($e->getMessage());
            }

            return [];
        }
    }

    public function convertToUnix($path)
    {
        return str_replace('\\', '/', $path);
    }

    public function readNexusConfig($portalDirectory)
    {
        $phpPath = $this->phpPath;
        $script = $this->scriptsDir . '\read_orgchart_config.php';//var_dump($phpPath . ' ' . $script . ' ' . $portalDirectory);
        $result = shell_exec($phpPath . ' ' . $script . ' ' . $portalDirectory) ;//var_dump($result);
        return unserialize($result);
    }

    public function readPortalConfig($portalDirectory)
    {
        $phpPath = $this->phpPath;
        $script = $this->scriptsDir . '\read_portal_config.php';//var_dump($phpPath . ' ' . $script . ' '. $portalDirectory);
        $result = shell_exec($phpPath . ' ' . $script . ' '. $portalDirectory);//var_dump($result);
        return unserialize($result);
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$phpPath = "php";
$scriptsPath = "C:\Users\jerem\Documents\GitHub\LEAF\LEAF_mig_work\LEAF_mig_scripts";
$sitesPath = "C:\Users\jerem\Documents\GitHub\LEAF\LEAF_mig_work\LEAF_SITES";
$outputPath = "C:\Users\jerem\Documents\GitHub\LEAF\LEAF_mig_work\siteStructure.json";

$sh = new SiteHelper($phpPath, $scriptsPath);

$sitesFound = $sh->parseSiteDirectory($sitesPath);

$organizedSites = $sh->organizeSites($sitesFound);

file_put_contents ( $outputPath , json_encode($organizedSites));

