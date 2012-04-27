<?php

$rootDir		=	realpath(dirname(__FILE__) . '/../../');
$jsonConf		=	$rootDir . '/~cache/conf.json';

!defined('CONFDIR') ?
		define('CONFDIR', $rootDir . '/conf') : false;

!defined('PRODUCT') ?
		(define('PRODUCT', $_SERVER['PRODUCT'] ? $_SERVER['PRODUCT'] : 'default')) : false ;

!defined('ENVIRONMENT') ?
		(define('ENVIRONMENT', $_SERVER['ENVIRONMENT'] ? $_SERVER['ENVIRONMENT'] : include CONFDIR . "/environment" )) : false ;

!defined('APPLICATION') ?
	define('APPLICATION',   $_SERVER['APPLICATION']) : null ;

include $rootDir . "/core/system/sys.php";
include $rootDir . "/core/system/router.class.php";



class conf
{
    static $conf = false;
}

if (file_exists($jsonConf))
{
	conf::$conf	= json_decode(file_get_contents($jsonConf), true);
}
else
{
	$globalConfig		= 	include dirname(__FILE__) . '/app.global.php';
	$productConfig		=	include $confDir . '/' . PRODUCT . ".all.php";
	$environmentConfig	=	include $confDir . '/' . PRODUCT . '.' . ENVIRONMENT . ".php";

	$config	=	_amr($globalConfig, $productConfig);
	conf::$conf	= _amr($applicationConfig, $environmentConfig);

	file_put_contents($rootDir . '/~cache/conf.json', json_encode(conf::$conf));
}

router::init(APPLICATION);

function _amr($a, $b)
{
	foreach($b as $key => $value)
	{
		if(array_key_exists($key, $a) && is_array($value))
		{
			$a[$key] = _amr($a[$key], $b[$key]);
		}
		else
		{
			$a[$key] = $value;
		}
	}

	return $a;
}