<?php

date_default_timezone_set('Europe/Helsinki');

$arguments = parseArguments($argv);

if (!$arguments)
{
	printUsage();
	exit (1);
}

if (!isset($arguments['product']))
{
	$arguments['product'] = 'default';
}

if (!isset($arguments['target']))
{
	$targets[]	=	'core';
}
else
{
	$targets	=	explode(',', $arguments['target']);
}


if (!isset($arguments['environment']))
{
	printError('--environment not found');
	printUsage();
	exit(1);
}

if (isset($arguments['confdir']))
{
	define('CONFDIR', $arguments['confdir']);
}

if (isset($arguments['application']))
{
	define('APPLICATION', $arguments['application']);
}

$cacheDir = dirname(__FILE__) . '/../~cache';

if (!is_dir($cacheDir))
{
	mkdir($cacheDir);
}

define('PRODUCT',	$arguments['product']);
define('ENVIRONMENT', $arguments['environment']);


if ($arguments['target']=='app' && !isset($arguments['application']))
{
	printError('--application not specified');
	printUsage();
	exit(1);
}


$rootdir = dirname(__FILE__) . "/..";

// include dirname(__FILE__) . "/conf/init.php";
include dirname(__FILE__) . "/system/builder.class.php";

foreach($targets as $target)
{
    include dirname(__FILE__) . "/builder/" . $arguments['target'] . ".php";

}


function printError($error)
{
echo
"
***********************************************************
ERROR: " . $error . "
***********************************************************
";
}

function printUsage()
{
echo
"
-----------------------------------------------------------
USAGE: build.php options
-----------------------------------------------------------
--product	- product name. Default `default`
--environment	- application environment
--target	- targets to execute (comma separated)
--confdir	- directory with configuration files
-----------------------------------------------------------
possible targets
1) core
2) database
3) app:[appname]
";
}


function parseArguments($arguments)
{
	array_shift($arguments);

	foreach($arguments as $argument)
	{
		if (substr($argument, 0, 2) != '--')
		{
			continue;
		}

		if (strpos($argument, '=') === false)
		{
			continue;
		}

		$arg = explode('=', $argument);
		$items[trim(substr($arg[0], 2))] = trim($arg[1]);
	}
	
	return $items;
}


?>