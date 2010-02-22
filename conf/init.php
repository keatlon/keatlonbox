<?php

if (!$_SERVER['PRODUCT'])
{
	$_SERVER['PRODUCT'] = 'default';
}

define('ENVIRONMENT',   $_SERVER['ENVIRONMENT']);
define('APPLICATION',   $_SERVER['APPLICATION']);
define('PRODUCT',		$_SERVER['PRODUCT']);

if ($_SERVER['CONFDIR'])
{
	define('CONFDIR',	$_SERVER['CONFDIR']);
}
else
{
	define('CONFDIR',	dirname(__FILE__) . '/../../conf');
}

class conf
{
    static protected $conf = false;

	/**
     *  Sources directory
     */
    public $rootdir			= false;


	/**
     *  Web domains. Available keys
     *  @var web		—	main applicatin domain
	 *	@var static	—	static domain
	 *	@var image	—	image domain
	 *	@var cookie	—	cookie domain. must start with point.
     */
    public $domains			= false;


	/**
     *  Debug information
     *  @var bool enable				—	enable/disable debugging
	 *	@var bool display_errors		—	display errors
	 *	@var display_level
	 *	@var log_level
	 *	@var log_errors			—	file to log PHP errors
	 *	@var log_exceptions		—	file to log application exceptions
	 *	@var log_information	—	file to log custom information
     */
	public $debug			= false;

	
    public $counter			= false;
    public $i18n			= false;
    public $application		= false;
    public $access			= false;
    public $ad				= false;
	public $email			= false;
    public $memcache		= false;
    public $mdb				= false;
    public $redis			= false;
    public $database		= false;
    public $sphinx			= false;
    public $video			= false;
    public $supersalt		= false;
    public $image			= false;
    public $captcha			= false;

    /**
     *
     * @return conf
     */
    static function i()
    {
        if (!self::$conf)
        {
            self::$conf = new conf;

        }

        return self::$conf;
    }
}

$globalConfig		= include dirname(__FILE__) . '/app.global.php';
$productConfig		= include CONFDIR . '/' . PRODUCT . ".all.php";
$environmentConfig	= include CONFDIR . '/' . PRODUCT . '.' . ENVIRONMENT . ".php";

$conf  = array_merge_recursive_distinct(array_merge_recursive_distinct($globalConfig, $productConfig) , $environmentConfig);

foreach($conf as $key => $value)
{
	conf::i()->$key = $value;
}


include conf::i()->rootdir . "/core/system/sys.php";
include conf::i()->rootdir . "/core/system/router.class.php";

router::init(APPLICATION);

function array_merge_recursive_distinct ( array &$array1, array &$array2 )
{
  $merged = $array1;

  foreach ( $array2 as $key => &$value )
  {
    if ( is_array ( $value ) && isset ( $merged [$key] ) && is_array ( $merged [$key] ) )
    {
      $merged [$key] = array_merge_recursive_distinct ( $merged [$key], $value );
    }
    else
    {
      $merged [$key] = $value;
    }
  }

  return $merged;
}
?>
