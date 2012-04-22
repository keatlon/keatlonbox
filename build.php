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
		case 'bigmigrate':
			bigmigrate($rootdir);
			break;


		case 'autoload':
			autoload($rootdir);
			break;

		case 'db':
			database();
			break;

		case 'form':
			forms($rootdir, $app);
			break;

		case 'static':

			$conf 		= 	include conf::i()->rootdir . '/conf/' . PRODUCT . '.static.php';

			foreach ($conf as $group => $files)
			{
				$info = pathinfo($group);

				switch($info['extension'])
				{
					case 'css':
						resource::build($group, 'css');
						break;

					case 'js':
						resource::build($group, 'js');
						break;
				}
			}

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


function database()
{
	foreach(conf::i()->database['pool'] as $dbName => $dbConnection)
	{
		$dbName     =   conf::i()->database['pool']['master']['dbname'];
		$tables     =   db::rows('SHOW TABLES FROM `' . $dbName . '`');
		$modelPath  =   conf::i()->rootdir . '/lib/model';

		foreach($tables as $table)
		{
			$tableName = $table['Tables_in_' . $dbName];
			$parts = explode('_', $tableName);

			if (count($parts) > 1)
			{
				for($l = 1; $l < count($parts);$l++)
				{
					$parts[$l] = ucfirst($parts[$l]);
				}
			}

			$className = implode('', $parts) . 'Peer';
			$classPath = $modelPath . '/' . $className . '.class.php';

			// GENERATE BASE CLASS
			$primaryKey = 'id';
			$fields     = array();

			$columns = db::rows('show columns from `' . $tableName . '`');

			$primaryKeys	=	array();

			$primaryFields	=	array();
			$primaryTFields	=	array();
			$primaryOFields	=	array();
			$primaryBinds	=	array();
			$fields			=	array();

			foreach($columns as $column)
			{
				if ($column['Key'] == 'PRI')
				{
					$primaryKeys[] = $column['Field'];
				}

				$fields[]	=	"'" . $column['Field'] . "'";
			}

			foreach ($primaryKeys as $primaryField)
			{
				$primaryFields[]	=	"'" . $primaryField . "'";
				$primaryTFields[]	=	"'" . $tableName . "." . $primaryField . "'";
				$primaryOFields[]	=	"'" . $tableName . "." . $primaryField . " DESC'";

				$primaryBinds[]		=	" " . $primaryField . " = :" . $primaryField . " ";
			}

			$primaryKey		=	"array(" . implode(',', $primaryFields) .  ")";
			$primaryTKey	=	"array(" . implode(',', $primaryTFields) .  ")";
			$primaryOKey	=	"array(" . implode(',', $primaryOFields) .  ")";
			$primaryBind	=	implode('AND', $primaryBinds);
			$multiPrimary	=	count($primaryKeys) > 1 ? 'true' : 'false';


			$baseClassName = implode('', $parts) . 'BasePeer';
			$baseClassPath = $modelPath . '/base/' . $baseClassName . '.class.php';
			$xml = simplexml_load_file(conf::i()->rootdir . '/core/database/basePeerClass.xml');
			$baseClassContent = str_replace('%BASECLASSNAME%', $baseClassName, $xml->body);
			$baseClassContent = str_replace('%CLASSNAME%', $className, $baseClassContent);
			$baseClassContent = str_replace('%TABLENAME%', $tableName, $baseClassContent);
			$baseClassContent = str_replace('%PRIMARYKEY%', $primaryKey, $baseClassContent);
			$baseClassContent = str_replace('%PRIMARYTKEY%', $primaryTKey, $baseClassContent);
			$baseClassContent = str_replace('%PRIMARYOKEY%', $primaryOKey, $baseClassContent);
			$baseClassContent = str_replace('%PRIMARYBIND%', $primaryBind, $baseClassContent);
			$baseClassContent = str_replace('%MULTIPRIMARY%', $multiPrimary, $baseClassContent);

			$baseClassContent = str_replace('%FIELDS%', implode(',', $fields), $baseClassContent);


			file_put_contents($baseClassPath, $baseClassContent);

			$xml = simplexml_load_file(conf::i()->rootdir . '/core/database/peerClass.xml');
			$classContent = str_replace('%BASECLASSNAME%', $baseClassName, $xml->body);
			$classContent = str_replace('%CLASSNAME%', $className, $classContent);
			if (!file_exists($classPath))
			{
				file_put_contents($classPath, $classContent);
			}
		}

		echo count($tables) . ' tables found';
	}
}


function autoload($rootdir)
{
	$files		=	scan($rootdir, '|.*\.php$|');
	$classes	=	array();
	$apps		=	array();

	foreach ($files as $file)
	{
		$isClass = preg_match('#/apps/(.*)/.*/([a-zA-Z0-9]+)\.class\.php#U', $file, $matches);

		if ($isClass)
		{
			$classes[$matches[1]][$matches[2]] = $file;
			continue;
		}


		$isCoreClass = preg_match('#/(core|lib)+.*/([a-zA-Z0-9]+)\.class\.php#U', $file, $matches);

		if ($isCoreClass)
		{
			$classes['core'][$matches[2]] = $file;
			continue;
		}
	}

	foreach($classes as $app => $files)
	{
		printf("%s classes in %s \n", count($files), $app);
		file_put_contents($rootdir . "/~cache/autoload-" . $app . ".php", "<?php return " . var_export($files, true) . ';');
	}
}

function forms($rootdir, $application)
{
	$formPath	=   conf::i()->rootdir . '/lib/form';
	$views		=	scan($rootdir . "/apps/" . $application, '|.*\.view\.php|');
	$template	=	simplexml_load_file(conf::i()->rootdir . '/core/builder/form.xml');

	foreach ($views as $filename)
	{
		$content	=	file_get_contents($filename);
		$res = preg_match_all('|<form.*action=[\'"]{1}(.*)[\'"]{1}.*>|U', $content, $matches);

		if (!$matches[1])
		{
			continue;
		}

		foreach($matches[1] as $action)
		{
			$classname		=	implode('', array_map('ucfirst', explode('/', $action))) . 'BaseForm';
			$classname{0}	=	strtolower($classname{0});
			$classFilename	=	$formPath . '/' . $classname . '.class.php';

			if (file_exists($classFilename))
			{
				continue;
			}

			file_put_contents($classFilename, str_replace('%BASECLASSNAME%', $classname, $template->body));
		}
	}
}

function bigmigrate($rootdir)
{
	$actions		=	scan($rootdir, '|.*\.action\.php$|U');
	$tasks			=	scan($rootdir, '|.*\.task\.php$|U');

	foreach ($actions as $action)
	{
		preg_match('|(.*)/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)\.action\.php$|', $action, $matches);
		$newfile = $matches[1] . '/' . $matches[2] . '/' . $matches[3] . '/' . $matches[4] . ucfirst($matches[2]) . 'Controller.class.php';

//		d('rename ' . $action . ' to ' . $newfile . "\n");

//		rename($action, $newfile);
	}


	foreach ($tasks as $task)
	{
		preg_match('|(.*)/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)\.task\.php$|', $task, $matches);
		$newfile = $matches[1] . '/' . $matches[2] . '/' . $matches[3] . '/' . $matches[4] . ucfirst($matches[3]) . 'Controller.class.php';
		rename($task, $newfile);
	}

	// $isAction = preg_match('#/apps/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/(.*)/([a-zA-Z0-9]+)\.action\.php#U', $file, $matches);
}
