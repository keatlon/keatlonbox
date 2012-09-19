<?php

class redis
{
	protected static $instance;

	/**
	 *	get instance of redis
	 *
	 *	@return Predis\Client
	 */
	static function i()
	{
		if (!self::$instance)
		{
			require_once conf::$conf['rootdir'] . '/lib/plugins/redis/Predis/Autoloader.php';
			Predis\Autoloader::register();

			self::$instance =	new Predis\Client(array(
			    'host'   				=>	conf::$conf['redis']['host'],
			    'port'   				=>	conf::$conf['redis']['port'],
				'connection_persistent'	=>	true,
			), array(
				'prefix' => conf::$conf['redis']['prefix'] . ':'
			));
		}

		return self::$instance;
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

	static function del($key, $inc = 1)
	{
		return self::i()->del($key, $inc);
	}

}
