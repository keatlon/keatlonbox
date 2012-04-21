<?php
class build
{

	static function database()
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


	static function autoload($rootdir)
	{
		$files		=	self::scan($rootdir, '|.*\.php$|');
		$classes	=	array();
		$apps		=	array();

		foreach ($files as $file)
		{
			$isAction = preg_match('#/apps/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/(.*)/([a-zA-Z0-9]+)\.(action|class)\.php#U', $file, $matches);

			if ($isAction)
			{
				list($path, $application, $module, $type, $name, $ext) = $matches;

				$apps[$application] = true;

				if ($type == 'action')
				{
					$classes[$application][$name . ucfirst($module) . 'Controller'] = $file;
				}

				if ($type == 'form')
				{
					$classes[$application][$name] = $file;
				}

				continue;
			}

			$isCoreClass = preg_match('#/(core|lib)/(.*)\.class\.php#U', $file, $matches);

			if ($isCoreClass)
			{
				$info = pathinfo($matches[2]);
				$classes['core'][$info['filename']] = $file;
				continue;
			}


			$isCoreAction = preg_match('#/core/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)/(.*)/([a-zA-Z0-9]+)\.action\.php#U', $file, $matches);

			if ($isCoreAction)
			{
				list($path, $application, $module, $type, $name, $ext) = $matches;
				$classes['core'][$name . ucfirst($module) . 'Controller'] = $file;
				continue;
			}


			$isTask = preg_match('#/(core|lib)/task/(.*)/(.*)\.task\.php#', $file, $matches);

			if ($isTask)
			{
				list($path, $type, $module, $action) = $matches;
				$classes['core'][$action . ucfirst($module) . 'Controller'] = $file;
				continue;
			}
		}

		foreach($classes as $app => $files)
		{
			printf("%s classes in %s \n", count($files), $app);
			file_put_contents($rootdir . "/~cache/autoload-" . $app . ".php", "<?php return " . var_export($files, true) . ';');
		}
	}

	static function forms($rootdir, $application)
	{
		$formPath	=   conf::i()->rootdir . '/lib/form';
		$views		=	self::scanViews($rootdir . "/apps/" . $application);
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

    static function scan($dir, $regexp)
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
			if ($file == '.' || $file == '..' )
			{
				continue;
			}

            if (is_dir($dir . '/' .$file))
            {
                $files = array_merge($files, (array)self::scan($dir . '/' . $file, $regexp));
            }
            else
            {
                if (preg_match($regexp, $file, $matches))
                {
                    $files[] = $dir . '/' . $file;
                }
            }

        }

        closedir($handle);
        return $files;
    }


}
