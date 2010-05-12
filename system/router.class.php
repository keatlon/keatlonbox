<?php

class router
{
    static private $classRoutes = null;
	
    static public function init($application = 'frontend')
    {
        include conf::i()->rootdir . "/~cache/autoload-core.php";
		
        if ($application)
        {
			if (file_exists(conf::i()->rootdir . "/~cache/autoload-{$application}.php"))
			{
				include conf::i()->rootdir . "/~cache/autoload-{$application}.php";
			}
        }
        
        self::$classRoutes = array_merge((array)$coreClasses, (array)${$application . 'Classes'});
    }
	
    static public function get($className)
    {
        if ( !isset(self::$classRoutes[$className]) )
        {
            return false;
        }
		
        return self::$classRoutes[$className];
    }
}

?>