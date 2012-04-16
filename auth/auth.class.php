<?php

class auth
{
	static protected $instances;
	static protected $gateway = false;

	/**
	 * get instance of auth class
	 *
	 * @param type enum server|cp|guest|web|iphone
	 * @return authBase
	 */
	static function i($type = false)
	{
		if (!$type)
		{
			$type = self::$gateway;
		}

		if ( !self::$instances[$type])
		{
			$className = 'auth' . ucfirst($type);
			self::$instances[$type] = new $className;
		}

		return self::$instances[$type];
	}

	static function authorize($data)
	{
		return self::i()->authorize($data);
	}

	static function mongoId()
	{
		return _mongo::primary(self::i()->getCredentials());
	}

	static function id()
	{
		return self::i()->id();
	}
	
	static function set($userId, $role = 'member')
	{
		return self::i()->set($userId, $role);
	}

	static function clear()
	{
		return self::i()->clear();
	}

	public static function me($id)
	{
		return call_user_func_array(array(self::i(), __FUNCTION__), func_get_args());
	}

	static function gateway($gateway = false)
	{
		if ($gateway)
		{
			self::$gateway = $gateway;
		}

		return self::$gateway;
	}

	static function init()
	{
		if (!conf::i()->application[APPLICATION]['auth'])
		{
			conf::i()->application[APPLICATION]['auth'] = array('server');
		}

		foreach(conf::i()->application[APPLICATION]['auth'] as $authEngine)
		{
			if (auth::i($authEngine)->id())
			{
				auth::gateway($authEngine);
			}
		}

		if (!auth::gateway())
		{
			if (is_array(conf::i()->application[APPLICATION]['auth']))
			{
				auth::gateway(conf::i()->application[APPLICATION]['auth'][0]);
			}
			else
			{
				auth::gateway('server');
			}
		}
		
	}

	static function role()
	{
		return self::i()->role();
	}

	static function roleIn()
	{
		$roles	=	func_get_args();
		$role	=	auth::role();

		if (is_array($roles))
		{
			return in_array($role, $roles);
		}

		return (bool)($roles == $role);
	}

}