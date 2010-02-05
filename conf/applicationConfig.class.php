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

    public $site_is_down	=	false;
    public $developers_ip	=	array ( '127.0.0.1' );

    /***********************************************************
     * 			ACCESS
     ***********************************************************/
    public $moderators	=	array(
		'admin'	=> array(
            'keatlon@gmail.com',
            'stanislav.starcha@gmail.com',
			'kykapeky@yahoo.com',
			'pogodin@gmail.com'
        )
    );


    /***********************************************************
     * 			EMAIL
     ***********************************************************/

	 public $email	= array(
		 'name_from'	=> 'BeWiBo',
		 'email_from'	=> 'noreply@bewibo.com',
		 'return_path'		=> 'www-data@bewibo.com',
	 );

    /***********************************************************
     *                      PATH
     ***********************************************************/
    public $rootdir 		= 	'f:/www/bwb';

    /***********************************************************
     *                      WEB DOMAINS
     ***********************************************************/
    public $domains			=	array(
        'web'						=> 	'http://bwb.dev',
        'admin'						=> 	'http://admin.bwb.dev',
        'static'					=> 	'http://s.bwb.dev',
        'image'						=> 	'http://i.bwb.dev',
        'video'						=> 	'http://v.bwb.dev',
        'cookie'					=> 	'.bwb.dev',
    );

    /***********************************************************
     *                  DEPLOYMENT
     ***********************************************************/
    public $deployment	=	array(
		'root_dir'	    => 'f:/www/bwb/deployment',
		'svn_user'	    => 'keatlon',
		'svn_password'  => 'playunfuddle',
		'svn_url'	    => 'http://keatlon.unfuddle.com/svn/keatlon_bwb/deployment',

		'bin'	=> array(
			'php'	=> 'f:/tools/PHP/php.exe',
			'svn'	=> 'f:/tools/svn/bin/svn.exe',
			'sql'	=> 'f:/tools/mysql-sphinx/bin/mysql.exe'
		),

		'users'			=>	array(
			1 => array( 'email' => 'keatlon@gmail.com', 'password' => 'test')
		),

		'dev'   => array(
			'source_dir'	=> 'c:/www/bwb/trunk',
			'svn_url'		=> 'http://keatlon.unfuddle.com/svn/keatlon_bwb/trunk',
		)
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
        'engine'    => 'xml'
    );

    public $application    =   array(
        'frontend' => array(
            'auth'  => array('server'),
            'i18n'  =>  array(
                'defaultLocale' => 'en',
                'ns'    => array('index', 'iphone', 'iphoneweb')
            ),
            'default'   => array(
                'signedin'  => array('start', 'index'),
                'signedout' => array('start', 'index'),
            ),
            'startSession'  => true
        ),

        'cp' => array(
            'auth'  => array('cp'),
            'i18n'  =>  array(
                'defaultLocale' => 'en',
                'ns'    => array('index')
            ),

            'default'   => array(
                'signedin'  => array('do', 'index'),
                'signedout' => array('account', 'signin'),
            ),

            'startSession'  => true,
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
		'storage'	=> 	'f:/www/storage/bwb',
		'cache'		=> 	'f:/www/bwb/web/storage',
		'imagick'	=>	'c:\soft\ImageMagick\convert.exe',
		'escapecmd' =>  true,
		'source'	=>	'c',

		'sizes' => array
		(
			't'	    => ' -quality 90 -strip -resize 100x100! -interlace line',
			'm'	    => ' -quality 90 -strip -resize 320x320! -interlace line',
			'u'	    => ' -quality 90 -strip -resize 500x500> -interlace line',
			'150x150'   => ' -quality 90 -strip -resize 150x150! -interlace line',
		),
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
            include dirname(__FILE__) . '/../../conf/app.' . ENVIRONMENT . ".config.php";
            $className = ENVIRONMENT . 'Config';
            self::$configuration = new $className;

        }

        return self::$configuration;
    }
}

include conf::i()->rootdir . "/core/system/sys.php";
include conf::i()->rootdir . "/core/system/router.class.php";

router::init(APPLICATION);
