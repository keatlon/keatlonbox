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
	printUsage();
	exit (1);
}

$targets	=	explode(',', $arguments['target']);

if (isset($arguments['confdir']))
{
	define('CONFDIR', $arguments['confdir']);
}

define('PRODUCT',	$arguments['product'] ? $arguments['product'] : 'default');

$rootdir        =   dirname(__FILE__) . "/..";

include dirname(__FILE__) . "/conf/init.php";
include dirname(__FILE__) . "/system/build.class.php";

$cacheDir = $rootdir . conf::i()->cachedir;

if (!is_dir($cacheDir))
{
	mkdir($cacheDir);
}

foreach($targets as $target)
{
	switch($target)
	{
		case 'autoload':
			build::all($rootdir);
			break;

		case 'db':
			build::database();
			break;

		case 'form':
			build::forms($rootdir, $app);
			break;

		case 'static':
			build::css();
			build::javascript();
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
--target	- targets to process (comma separated)
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
