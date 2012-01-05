<?php

return array
(
    /***********************************************************
     *                      DIRS
     ***********************************************************/
    'rootdir'		=> 	'',

    /***********************************************************
     *                      DOMAINS
     ***********************************************************/

    'domains'			=>	array(
        'web'		=> 	'',
        'static'	=> 	'',
        'image'		=> 	'',
        'video'		=> 	'',
        'cookie'	=> 	'.',
    ),

    /***********************************************************
     *                      DEBUG
     ***********************************************************/
	'debug'                     =>       array(
		'enable'					=>	false,
		'display_errors'            =>  false,
		'display_level'				=>	E_ALL & ~E_NOTICE,
		'log_level'					=>	E_ALL & ~E_NOTICE,
		'log_errors'                =>  false,
		'log_exceptions'            =>  false,
		'log_information'           =>  false,
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

    'counters'         =>   array(
        'ga'          =>  false,
        'km'          =>  false
    ),

    /***********************************************************
     *                      I18N
     ***********************************************************/

    'i18n'    =>   array(
		'enabled'	=>	true,
        'engine'    =>	false
    ),

    /***********************************************************
     *                      APPLICATION
     ***********************************************************/

    'application'    =>   array
	(
		'timezone'	=>	'Europe/Helsinki',
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

    /***********************************************************
     *                      AD
     ***********************************************************/
    'ad'         =>   array
	(
        'enabled'                   =>  false,
        '300x250'                   =>  false,
        'top'                       =>  false
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
     *                      SPHINXSEARCH
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
		'public_key'	=> 	'',
		'private_key'	=> 	'',
    ),


    /***********************************************************
     *                      FACEBOOK
     ***********************************************************/

    'facebook' => array
    (
		'id'		=> 	'',
		'key'		=> 	'',
		'secret'	=> 	'',
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