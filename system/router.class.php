<?php

class router
{
    static private $classRoutes = array();
	static private $ns = array();

    static public function init($application = false)
    {
		if ($application)
		{
			self::$classRoutes = array_merge(
				require_once conf::$conf['rootdir'] . "/~cache/autoload-core.php",
				require_once conf::$conf['rootdir'] . "/~cache/autoload-$application.php"
			);
		}
		elseif (file_exists(conf::$conf['rootdir'] . "/~cache/autoload-core.php"))
		{
			self::$classRoutes = require_once conf::$conf['rootdir'] . "/~cache/autoload-core.php";
		}
    }
	
    static public function get($className)
    {
        return self::$classRoutes[$className];
    }

	static public function register($root, $directory)
	{
		$ns[$root]	=	$directory;
	}

}
