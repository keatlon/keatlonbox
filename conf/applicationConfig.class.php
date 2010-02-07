<?php

abstract class applicationConfig
{

    /***********************************************************
     *                      DIR
     ***********************************************************/

    public $rootdir		= 	'';

    /***********************************************************
     *                      DOMAINS
     ***********************************************************/
    public $domains			=	array(
        'web'		=> 	'',
        'static'	=> 	'',
        'image'		=> 	'',
        'video'		=> 	'',
        'cookie'	=> 	'.',
    );

    /***********************************************************
     *                  DEBUG
     ***********************************************************/

	public $debug                     =       array(
		'enable'					=>	false,
		'display_errors'            =>  false,
		'log_errors'                =>  false,
		'log_exceptions'            =>  false,
		'log_information'           =>  false,
	);

	/*
	* Enable google analitycs
	*/
    public $counter         =   array(
        'google_analytics'          =>  false
    );

    /***********************************************************
     *			I18N
     ***********************************************************/

    public $i18n    =   array(
        'engine'    => 'inline'
    );

    /***********************************************************
     *			APPLICATIONS
     ***********************************************************/

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

        'image' => array(
            'startSession'  => false
        )
    );

    /***********************************************************
     * 			ACCESS
     ***********************************************************/

    public $access	=	array(

		/*
		 * access group
		 */
		'global'	=> array(
			/*
			 * array of emails
			 */
		)
    );
	
    /***********************************************************
     * 				AD
     ***********************************************************/
    public $ad         =   array(
        'enabled'                   =>  false,
        '300x250'                   =>  false,
        'top'                       =>  false
    );

    /***********************************************************
    * 			EMAIL
    ***********************************************************/
	 public $email	= array
	(
		 'name_from'	=> 'keatlonbox',
		 'email_from'	=> 'postmaster@keatlonbox.com',
		 'return_path'	=> 'postmaster@keatlonbox.com',
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
			
            'prefix'            => '',
			'enabled'           => false,
        ),
	
    );

    /***********************************************************
    * 			MEMCACHE DATABASE
    ***********************************************************/
    public $mdb		= array
    (
	    'host'              => 'localhost',
	    'port'              => 21201,
	    'enabled'           => false
    );

    /***********************************************************
    * 			REDIS DATABASE
    ***********************************************************/
    public $redis		= array
    (
	    'prefix'            => '',
	    'host'              => 'localhost',
	    'port'              => 6379,
	    'enabled'           => false
    );

    /***********************************************************
     *			MYSQL	DATABASE
     ***********************************************************/
    public $database		=	array
    (
		'default_connection' => 'master',
		'pool'	=> array
		(
			'master' => array
			(
				'host' 		=>	'localhost',
				'user' 		=>	'root',
				'password' 	=>	'root',
				'dbname'	=>	'bwb',
				'enabled'	=>	true
            ),
        )
    );

	/***********************************************************
	 * 			SPHINX
	 ***********************************************************/

    public $sphinx         =   array(
        'config_path'               =>  '',
        'pid_path'                  =>  '',
        'log_path'                  =>  '',
        'log_query_path'            =>  '',
        'storage_path'              =>  '',
        'database'                  =>  'master',
        'port'                      =>  3312
    );

	/***********************************************************
	 * 		VIDEO
	 ***********************************************************/

    public $video = array
    (
		'storage'	=> 	'',
		'cache'		=> 	'',

		'encoder'	=> 'ffmpeg',
		'sizes'		=> array(
			'normal' => ' -b 1000kb/s -r 32 -y -ar 22050 '
        )
    );

	/***********************************************************
	 *			IMAGES
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

        'watermark' => array ()

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

include dirname(__FILE__) . '/init.php';