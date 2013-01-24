<?php

class redis
{
	protected static $instances;

	/**
	 *	get instance of redis
	 *
	 *	@return Predis\Client
	 */
	static function i($connection = 'default')
	{
		if (!self::$instances[$connection])
		{
			require_once ROOTDIR . '/lib/plugins/redis/Predis/Autoloader.php';
			Predis\Autoloader::register();

			if (!conf::$conf['redis']['pool'][$connection])
			{
				$host	=	conf::$conf['redis']['host'];
    			$port   =	conf::$conf['redis']['port'];
			}
			else
			{
				$host	=	conf::$conf['redis']['pool'][$connection]['host'];
    			$port   =	conf::$conf['redis']['pool'][$connection]['port'];
			}

			self::$instances[$connection] =	new Predis\Client(array(
			    'host'   				=>	$host,
			    'port'   				=>	$port,
				'connection_persistent'	=>	true,
			), array(
				'prefix' => conf::$conf['redis']['prefix'] . ':'
			));
		}

		return self::$instances[$connection];
	}

	static function set($key, $value)
	{
		return self::i()->set($key, $value);
	}

	static function get($key)
	{
		return self::i()->get($key);
	}

	static function inc($key, $inc = 1)
	{
		return self::i()->incrby($key, $inc);
	}

	static function dec($key, $inc = 1)
	{
		return self::i()->decrby($key, $inc);
	}

	static function del($key)
	{
		return self::i()->del($key);
	}

}
