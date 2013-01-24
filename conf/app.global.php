<?php

return array
(
    /***********************************************************
     *                      DIRS
     ***********************************************************/
	'cachedir'		=> 	'/~cache',

    /***********************************************************
     *                      DOMAINS
     ***********************************************************/

    'domains'			=>	array(
        'web'		=> 	'',
        'static'	=> 	'',
        'cookie'	=> 	'.',
    ),

    /***********************************************************
     *                      DEBUG
     ***********************************************************/
	'debug'                     =>       array
	(
		'enable'				=>	false,
		'log_errors'            =>  false,
		'log_exceptions'        =>  false,
		'log_information'       =>  false,
		'log_mysql'           	=>  false,
	),

	/***********************************************************
	*                      PROFILER
	***********************************************************/
	'profiler'                     =>       array
	(
		'enabled'				=>	false,
	),

	/***********************************************************
	*                      PHP INI
	***********************************************************/

	'phpini'	=>	array
	(
		'display_errors'            =>  false,
		'display_level'				=>	E_ALL & ~E_NOTICE,
		'log_level'					=>	E_ALL & ~E_NOTICE,
		'date.timezone'				=>	'Europe/Helsinki'
	),

	/***********************************************************
    * 			STATICS FILES JS, CSS
    ***********************************************************/

	'system'	=>	array
	(
		'java'	=>	'java',
	),

	/***********************************************************
     * 			STATICS FILES JS, CSS
     ***********************************************************/

	'static'	=>	array
	(
		'js'	=>	array
		(
			'compile'		=>	false,
			'compiler'		=>	false,

			'compress'		=>	true,
			'compressor'	=>	'java -jar yuicompressor.jar --type JS %s > %s',
		),

		'css'	=>	array
		(
			'compile'		=>	false,
			'compiler'		=>	false,

			'compress'		=>	true,
			'compressor'	=>	'java -jar yuicompressor.jar --type JS %s > %s',
		),

		'compiled'		=>	'/web/static',
		'check'			=>	'modified'
	),

	/***********************************************************
     * 			ACCESS CONTROL LIST
     ***********************************************************/
	'acl'	=>	array
	(
		'*'						=>	'deny.*',
		'*.layout'				=>	'allow.*',
		'*.exception'			=>	'allow.*',
	),

    /***********************************************************
     *                      COUNTERS
     ***********************************************************/

    'counters'	=>   array
	(
        'googleanalytics'	=>  array
		(
			'enabled'	=>	false,
			'id'		=>	''
		),

		'kissmetrics'	=>  array
		(
			'enabled'	=>	false,
			'id'		=>	''
		),
    ),

    /***********************************************************
     *                      APPLICATION
     ***********************************************************/

    'application'    =>   array
	(
        'frontend'	=> array
		(
            'auth'  => array('mysql'),
			
            'default'   => array
			(
                'signedin'  => 	array('start', 'index'),
                'signedout' => 	array('start', 'index'),
				'layout'	=>	array('layout', 'index')
            ),
			
            'session'  => true
        ),

        'image' => array
		(
            'session'  => false
        ),

		'url'	=>	array
		(
			'parser'	=>	array('url', '_parse'),
			'builder'	=>	array('url', '_build'),
		)
    ),


	'router'	=>	array
	(
		'console'	=>	'frontend'
	),

	/***********************************************************
	*                      SESSION
	***********************************************************/

	'session'	=>	array
	(
		'handler'	=>	false
	),

    /***********************************************************
     *                      EMAIL
     ***********************************************************/

	 'email'	=>	array
	(
		 'enabled'		=>	true,
		 'name_from'	=>	'keatlonbox',
		 'email_from'	=>	'postmaster@keatlonbox.com',
		 'return_path'	=>	'postmaster@keatlonbox.com',
	 ),

    /***********************************************************
     *                      COMET
     ***********************************************************/

	'comet'			=>	array(
		'enabled'	=>	false,
		'push_url'	=>	'',
		'get_url'	=>	'',
	),

    /***********************************************************
     *                      MEMCACHED
     ***********************************************************/

    'memcache'		=>	array
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
			
            'prefix'            => '',
			'enabled'           => false,
        ),
    ),

    /***********************************************************
     *                      MEMCACHED DATABASE
     ***********************************************************/
    'mdb'		=> array
    (
	    'host'              => 'localhost',
	    'port'              => 21201,
	    'enabled'           => false
    ),

    /***********************************************************
     *                      REDIS
     ***********************************************************/
    'redis'		=> array
    (
	    'prefix'            => '',
	    'host'              => 'localhost',
	    'port'              => 6379,
	    'enabled'           => false
    ),

    /***********************************************************
     *                      MYSQL
     ***********************************************************/
    'database'		=>	array
    (
		'engine'				=>	'mysql',
		'default_connection'	=>	'master',
		'pool'	=> array
		(
			'master' => array
			(
				'host' 		=>	'localhost',
				'user' 		=>	'',
				'password' 	=>	'',
				'dbname'	=>	'',
				'enabled'	=>	true
            ),
        )
    ),

    /***********************************************************
     *                      SPHINX
     ***********************************************************/
    'sphinx'         =>   array(
        'config_path'               =>  '',
        'pid_path'                  =>  '',
        'log_path'                  =>  '',
        'log_query_path'            =>  '',
        'storage_path'              =>  '',
        'database'                  =>  'master',
        'port'                      =>  3312
    ),

    /***********************************************************
     *                      VIDEO
     ***********************************************************/
    'video' => array
    (
		'storage'	=> 	'',
		'cache'		=> 	'',

		'encoder'	=> 'ffmpeg',
		'sizes'		=> array(
			'normal' => ' -b 1000kb/s -r 32 -y -ar 22050 '
        )
    ),

    /***********************************************************
     *                      IMAGES
     ***********************************************************/

    'supersalt'	=> 'yEt @n0tHeR sA1t m#cH@N1Zm',

    'image' => array
    (
		'storage'	=> 	'',
		'cache'		=> 	'',
		'imagick'	=>	'',
		'escapecmd' =>  true,
		'source'	=>	'c',

		'sizes' => array (),

        'watermark' => array ()

    ),

    /***********************************************************
     *                      CAPTCHA
     ***********************************************************/

    'captcha' => array
    (
		'lib'		=>	'/lib/plugins/recaptcha',
		'public_key'	=> 	'',
		'private_key'	=> 	'',
    ),


    /***********************************************************
     *                      FACEBOOK
     ***********************************************************/

    'facebook' => array
    (
		'lib'		=>	'/lib/plugins/facebook',
		'id'		=> 	'',
		'key'		=> 	'',
		'secret'	=> 	'',
		'domain'	=>	''
    ),

	/***********************************************************
	*                      TWITTER
	***********************************************************/

	'twitter' => array
	(
		'lib'				=>	'',
		'authorizeUrl'		=>	'https://api.twitter.com/oauth/authorize',
		'requestTokenUrl'	=>	'https://api.twitter.com/oauth/request_token',
		'accessTokenUrl'	=>	'https://api.twitter.com/oauth/access_token',
		'localAuthorizeUrl'	=>	'/twitter/authorize',
	),

	/***********************************************************
	*                      INSTAGRAM
	***********************************************************/

	'instagram' => array
	(
		'lib'				=>	'',
		'authorizeUrl'		=>	'https://api.instagram.com/oauth/authorize',
		'requestTokenUrl'	=>	'https://api.instagram.com/oauth/request_token',
		'accessTokenUrl'	=>	'https://api.instagram.com/oauth/access_token',
		'localAuthorizeUrl'	=>	'/instagram/authorize',
	),


	/***********************************************************
	*                      VKONTAKTE
	***********************************************************/

	'vkontakte' => array
	(
		'id'				=>	'',
		'key'				=>	''
	),

	/***********************************************************
	*                      LESSPHP
	***********************************************************/

	'lessphp' => array
	(
		'lib'				=>	'/lib/plugins/lessphp',
	),

	/***********************************************************
	*                      LESSPHP
	***********************************************************/

	'purifier' => array
	(
		'lib'				=>	'/lib/plugins/htmlpurifier',
	),

	/***********************************************************
     * 			CLIENT (JS)
     ***********************************************************/

	'client'	=>	array
	(
		'ajax'			=>	array(),
		'facebook'		=>	array(),
		'comet'			=>	array(),
		'dialog'		=>	array(),
		'form'			=>	array(),
		'notification'	=>	array
		(
			'position'	=>	'bottom'
		)
	)
);