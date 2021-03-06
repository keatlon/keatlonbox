<?php

require_once dirname(__FILE__) . "/../system/sys.php";

/**************************************************************
 *         DEFINE PATHS & ENVIRONMENT
 ***************************************************************/

!defined('MODE') ? define('MODE', false) : false;

!defined('ROOTDIR') ? define('ROOTDIR', realpath(dirname(__FILE__) . '/../../')) : false;

!defined('CONFDIR') ? define('CONFDIR', ROOTDIR . '/conf') : false;

!defined('PRODUCT') ? (define('PRODUCT', isset($_SERVER['PRODUCT']) ? $_SERVER['PRODUCT'] : 'default')) : false;

!defined('ENVIRONMENT') ? (define('ENVIRONMENT', isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : include CONFDIR . "/environment")) : false;

require_once ROOTDIR . "/core/system/router.class.php";

if (isset($argv))
{
    $arguments = __parseArguments($argv);
}

class conf
{
    static $conf = false;
}

/**************************************************************
 *         BUILD CONFIG
 ***************************************************************/

$globalConfigFile      = dirname(__FILE__) . '/app.global.php';
$productConfigFile     = CONFDIR . '/' . PRODUCT . ".all.php";
$environmentConfigFile = CONFDIR . '/' . PRODUCT . '.' . ENVIRONMENT . ".php";

$recompile = false;

$metaFile = ROOTDIR . '/~cache/conf.meta';
$jsonFile = ROOTDIR . '/~cache/conf.json';

$changed  = max(filemtime($globalConfigFile), filemtime($productConfigFile), filemtime($environmentConfigFile));
$compiled = @file_get_contents($metaFile);

if (!file_exists($metaFile) || !file_exists($jsonFile) || ($changed > $compiled))
{
    $recompile = true;
}

if ($recompile)
{
    $globalConfig      = include $globalConfigFile;
    $productConfig     = include $productConfigFile;
    $environmentConfig = include $environmentConfigFile;

    $config     = _amr($globalConfig, $productConfig);
    conf::$conf = _amr($config, $environmentConfig);

    @file_put_contents($jsonFile, json_encode(conf::$conf));
    @file_put_contents($metaFile, $changed);
}
else
{
    conf::$conf = json_decode(file_get_contents($jsonFile), true);
}

$application = isset($arguments['application']) ? $arguments['application'] : false;

if (!$application)
{
    $application = isset($_SERVER['APPLICATION']) ? $_SERVER['APPLICATION'] : 'main';
}

!defined('APPLICATION') ? define('APPLICATION', $application) : false;

foreach (conf::$conf['phpini'] as $key => $value)
{
    ini_set($key, $value);
}

router::init(APPLICATION ? APPLICATION : false);