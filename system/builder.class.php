<?php
class builder
{

	static function buildDatabase()
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


	static function buildCore($rootdir)
	{
		$core           = builder::scanClasses($rootdir   . "/core");
		$tasks          = builder::scanTasks($rootdir     . "/core");
		$appTasks       = builder::scanTasks($rootdir     . "/lib");
		$components     = builder::scanClasses($rootdir   . "/lib");
		$actions        = builder::scanActions($rootdir   . "/core");
		$plugins		= builder::scanClasses($rootdir   . "/plugins");


		echo 'core: ' . (count($core) + count($actions) + count($components) + count($tasks) + count($appTasks) + count($plugins)) . " classes \n";

		file_put_contents($rootdir . "/~cache/autoload-core.php",
			"<?php \n\n " .
			"\$coreClasses = array( \n" .
				builder::arrayToCode($core) .
				builder::arrayToCode($tasks) .
				builder::arrayToCode($appTasks) .
				builder::arrayToCode($components) .
				builder::arrayToCode($plugins) .
				builder::arraytoCode($actions) .
				builder::arraytoCode($configuration)

			."); \n\n\n\n ?>"
		);
	}


	static function buildApplication($rootdir, $application)
	{
		$actions    	= 	builder::scanActions($rootdir . "/apps/" . $application);
		$classes    = builder::scanClasses($rootdir . "/apps/" . $application);

		echo $application . ': ' . count($actions) . " actions " . count($classes) . " classes \n";

		file_put_contents($rootdir . "/~cache/autoload-" . $application . ".php",
			"<?php \n\n " .
			"\$" . $application . "Classes = array( \n" . builder::arrayToCode($actions) .builder::arrayToCode($classes) . "); \n\n" .
			"\n\n ?>"
		);
	}

	static function buildForms($rootdir, $application)
	{
		$formPath	=   conf::i()->rootdir . '/lib/form';
		$views		=	builder::scanViews($rootdir . "/apps/" . $application);
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

    static protected function arrayToCode($items)
    {
        $result = '';

        if (!$items)
        {
            return $result;
        }

        foreach($items as $name => $path)
        {
            $result .= "\t'{$name}'\t\t\t\t=>'{$path}',\n";
        }

        return $result;
    }

    static protected function scanClasses($path)
    {
        $files = builder::_readdir($path, '|(.*)\.class\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9_]+)\.class\.php#', $path, $matches))
            {
                $result[$matches[1]] = $path;
            }
        }

        return (array)$result;
    }

    static protected function scanTasks($path)
    {
        $files = builder::_readdir($path, '|(.*)\.task\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)\.task\.php#', $path, $matches))
            {
                $result[$matches[2] . ucfirst($matches[1]) . 'Controller'] = $path;
            }
        }

        return $result;
    }

    static protected function scanActions($path)
    {
        $files = builder::_readdir($path, '|(.*)\.action\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9]+)/action/([a-zA-Z0-9]+)\.action\.php#', $path, $matches))
            {
                $result[$matches[2] . ucfirst($matches[1]) . 'Controller'] = $path;
            }
        }

        return $result;
    }

    static protected function scanViews($path)
    {
        $files = builder::_readdir($path, '|(.*)\.view\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9]+)/view/([a-zA-Z0-9]+)\.view\.php#', $path, $matches))
            {
                $result[$matches[2] . ucfirst($matches[1]) . 'BaseForm'] = $path;
            }
        }

        return $result;
	}
	
    static protected function scanPHP($path)
    {
        $files = builder::_readdir($path, '|(.*)\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9_\.]*)\.php#', $path, $matches))
            {
                $result[$matches[1]] = $path;
            }
        }

        return $result;
    }

    static protected function _scandirs($dir)
    {
        $files = array();
        $handle = opendir($dir);

        if (!$handle)
        {
            return false;
        }

        while (false !== ($file = readdir($handle)))
        {
            if ($file == '.' || $file == '..' || $file == '.svn' )
            {
                continue;
            }

            if (is_dir($dir . '/' .$file))
            {
                $files[]    =   $file;
            }
        }

        closedir($handle);
        return $files;


    }

    static protected function _readdir($dir, $pattern)
    {
        $files = array();

		if (!file_exists($dir))
		{
			return $files;
		}

        $handle = opendir($dir);

        if (!$handle)
        {
            return false;
        }

        while (false !== ($file = readdir($handle)))
        {
            if ($file == '.' || $file == '..' || $file == '.svn' )
            {
                continue;
            }

            if (is_dir($dir . '/' .$file))
            {
                $files = array_merge($files, (array)builder::_readdir($dir . '/' . $file, $pattern));
            }
            else
            {
                if (preg_match($pattern, $file, $matches))
                {
                    $files[] = $dir . '/' . $file;
                }
            }

        }

        closedir($handle);
        return $files;
    }

    static function getApps($rootdir)
    {
        return self::_scandirs($rootdir . '/apps');
    }
}
