<?php

require conf::i()->rootdir . '/core/library/facebook/facebook.php';

class fb
{
	private static $instance = false;

	/**
	 *
	 * @return Facebook
	 */
	static protected function i()
	{
		if (!self::$instance)
		{
			self::$instance = new Facebook(array
			(
				'appId'		=>	conf::i()->facebook['id'],
				'secret'	=>	conf::i()->facebook['secret'],
				'cookie'	=>	conf::i()->facebook['cookie'],
				'domain'	=>	conf::i()->facebook['domain'],
			));
		}

		return self::$instance;
	}

	static function id()
	{
		return self::i()->getUser();
	}

	static function info()
	{
		return self::i()->api('/me');
	}

	static function init()
	{
	}
}
?>
