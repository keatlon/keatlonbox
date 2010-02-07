<?php

if (!$_SERVER['PRODUCT'])
{
	$_SERVER['PRODUCT'] = 'default';
}

define('ENVIRONMENT',   $_SERVER['ENVIRONMENT']);
define('APPLICATION',   $_SERVER['APPLICATION']);
define('PRODUCT',		$_SERVER['PRODUCT']);

class conf
{
    static $configuration = false;

    /**
     *
     * @return applicationConfig
     */
    static function i()
    {
        if (!self::$configuration)
        {
			include dirname(__FILE__) . '/../../conf/app.' . PRODUCT . ".php";
			include dirname(__FILE__) . '/../../conf/' . PRODUCT . '/app.' . ENVIRONMENT . ".php";

            $className = ENVIRONMENT . 'Config';
            self::$configuration = new $className;

        }

        return self::$configuration;
    }
}

include conf::i()->rootdir . "/core/system/sys.php";
include conf::i()->rootdir . "/core/system/router.class.php";

router::init(APPLICATION);

?>
