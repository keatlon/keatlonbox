<?php

abstract class applicationConfig
{

    /***********************************************************
     *                  DEBUG
     ***********************************************************/
    public $debug		    =	false;

    public $log                     =   array(
		/*
		* filename to log PHP error
		*/
        'errors'                    =>  '',

		/*
		* filename to log exceptions
		*/
        'exception'                 =>  '',
    );

    /***********************************************************
     *                      PATH
     ***********************************************************/

    public $rootdir 		= 	'';

    /***********************************************************
     *                      WEB DOMAINS
     ***********************************************************/
    public $domains			=	array(
        'web'						=> 	'',
        'admin'						=> 	'',
        'static'					=> 	'',
        'image'						=> 	'',
        'video'						=> 	'',
        'cookie'					=> 	'.',
    );

    /***********************************************************
     * 				COUNTERS
     ***********************************************************/
    public $counter         =   array(
        'google_analytics'          =>  false
    );

	/***********************************************************
	 * 		APPLICATION
	 ***********************************************************/

    public $i18n    =   array(
        'engine'    => 'inline'
    );

    public $application    =   array(
        'frontend' => array(
            'auth'  => array('server'),
            'i18n'  =>  array(
                'defaultLocale' => 'en',
                'ns'    => array('index')
            ),
            'default'   => array(
                'signedin'  => array('start', 'index'),
                'signedout' => array('start', 'index'),
            ),
            'startSession'  => true
        ),

        'iphone' => array(
            'auth'  => array('iphone'),
            'i18n'  =>  array(
                'defaultLocale' => 'en',
                'ns'    => array('iphoneweb', 'index')
            ),
            'default' => array(
                'signedin'  => array('stats', 'index'),
                'signedout' => array('stats', 'index'),
            ),
            'startSession'  => false
        ),

        'api' => array(
			'renderer'	=> 'xml' ,
            'auth'		=> array('iphone'),
            'i18n'		=>  array(
                'defaultLocale' => 'en',
                'ns'    => array('index', 'iphone')
            ),

            'default' => array(
                'signedin'  => array('http', 'error'),
                'signedout' => false,
            ),

			// /i18n/getlabels/pid/6D501A9E-C100-5D23-AF7B-82FCC282B620/v/1.0/l/en_US
			// C217578D61F8E37B83152DF869CC35D9
			'key'	=> '&^WWE$#$DGaN#$LL@__!',
            'startSession'  => false
        ),

        'image' => array(
            'startSession'  => false
        )
    );

    /***********************************************************
    * 			APPSTORE
    ***********************************************************/
	public $appstore = array(
		'storeUrl'	=> 'https://buy.itunes.apple.com/verifyReceipt'
	);

    /***********************************************************
    * 			MEMCACHE
    ***********************************************************/
    public $memcache		= array
    (
        0 => array(
			'host'              => 'localhost',
			'port'              => 11211,
            'persistent'        => true,
            'weight'            => 1,
            'timeout'           => 1,
            'retry_interval'    => 15,
            'status'            => true,
            'failure_callback'  => NULL,
			'enabled'           => false
        ),
	
    );

    /***********************************************************
    * 			MEMCACHE DATABASE
    ***********************************************************/
    public $mdb		= array
    (
	    'host'              => 'localhost',
	    'port'              => 21201,
	    'enabled'           => true
    );

    /***********************************************************
    * 			REDIS DATABASE
    ***********************************************************/
    public $redis		= array
    (
	    'prefix'            => '',
	    'host'              => 'localhost',
	    'port'              => 6379,
    );

    /***********************************************************
     *                      DATABASE
     ***********************************************************/
    public $database		=	array
    (
		'default_connection' => 'master',
		'pool'	=> array(

			'master' => array(
				'host' 		=>	'localhost',
				'user' 		=>	'root',
				'password' 	=>	'root',
				'dbname'	=>	'bwb',
				'enabled'	=>	true
            ),

			'slave' => array(
				'host' 		=>	'localhost',
				'user' 		=>	'root',
				'password' 	=>	'root',
				'dbname'	=>	'bwb',
				'enabled'	=>	true
            )
        )
    );

	/***********************************************************
	 * 		IMAGES
	 ***********************************************************/

    public $supersalt	= 'yEt @n0tHeR sA1t m#cH@N1Zm';

    public $image = array
    (
		'storage'	=> 	'',
		'cache'		=> 	'',
		'imagick'	=>	'',
		'escapecmd' =>  true,
		'source'	=>	'c',

		'sizes' => array (),
    );

    /***********************************************************
     * 			CAPTHCA
     ***********************************************************/

    public $captcha = array
    (
		'public_key'	=> 	'6LeuZAcAAAAAAPFRowqDzGIuSwZTvCnAQwt0ORum',
		'private_key'	=> 	'6LeuZAcAAAAAAEVScP_47NwMcoYas0jgiPjLFzXD',
    );

}

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
			include dirname(__FILE__) . '/../../conf/' . PRODUCT . '/app.' . ENVIRONMENT . ".config.php";
			
            $className = ENVIRONMENT . 'Config';
            self::$configuration = new $className;

        }

        return self::$configuration;
    }
}

include conf::i()->rootdir . "/core/system/sys.php";
include conf::i()->rootdir . "/core/system/router.class.php";

router::init(APPLICATION);
