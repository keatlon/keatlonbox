<?php

class router
{
    static private $classRoutes = array();
	static private $ns = array();

    static public function init($application = false)
    {
        $coreAutoloads          =   ROOTDIR . "/~cache/autoload-core.php";
        $coreClasses            =   file_exists($coreAutoloads) ? require_once $coreAutoloads : array();

        $applicationAutoloads   =   ROOTDIR . "/~cache/autoload-$application.php";
        $applicationClasses     =   ($application && file_exists($applicationAutoloads)) ? require_once $applicationAutoloads : array();

        self::$classRoutes = array_merge($coreClasses, $applicationClasses);
    }
	
    static public function get($className)
    {
        return self::$classRoutes[$className];
    }

	static public function exists($module, $action)
	{
		return self::$classRoutes[$action . ucfirst($module) . 'Controller'];
	}

	static public function register($root, $directory)
	{
		$ns[$root]	=	$directory;
	}

}
