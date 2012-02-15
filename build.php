<?php

date_default_timezone_set('Europe/Helsinki');

$arguments = parseArguments($argv);

if (!$arguments)
{
	printUsage();
	exit (1);
}

if (!isset($arguments['target']))
{
	$targets	=	array
	(
		'core',
	);
}
else
{
	$targets	=	explode(',', $arguments['target']);
}

if (isset($arguments['confdir']))
{
	define('CONFDIR', $arguments['confdir']);
}

define('PRODUCT',	$arguments['product'] ? $arguments['product'] : 'default');

$cacheDir = dirname(__FILE__) . '/../~cache';

if (!is_dir($cacheDir))
{
	mkdir($cacheDir);
}

$rootdir        =   dirname(__FILE__) . "/..";

include dirname(__FILE__) . "/conf/init.php";

include dirname(__FILE__) . "/system/builder.class.php";


foreach($targets as $target)
{
	if (strpos($target, ':') !== false)
	{
		list($target, $app) = explode(':', $target);
	}

	switch($target)
	{
		case 'core':
			builder::buildCore($rootdir);
			break;

		case 'apps':
            $applist    =   builder::getApps($rootdir);

            foreach ($applist as $app)
            {
                builder::buildApplication($rootdir, $app);
            }
			break;

		case 'app':
			builder::buildApplication($rootdir, $app);
			break;

		case 'db':
			builder::buildDatabase();
			break;

		case 'form':
			builder::buildForms($rootdir, $app);
			break;

		case 'static':
			break;
	}
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
2) db
3) apps
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