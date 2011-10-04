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
	static function i($type = false, $application = false)
	{
		if (!$application)
		{
			$application = application::$name;
		}

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
		return self::i()->getCredentials();
	}
	
	static function getCredentials()
	{
		return self::i()->getCredentials();
	}

	static function getExtraCredentials()
	{
		return self::i()->getExtraCredentials();
	}

	static function createUser($data)
	{
		return self::i()->createUser($data);
	}

	static function setCredentials($userId, $role = 'member')
	{
		return self::i()->setCredentials($userId, $role);
	}

	static function setExtraCredentials($data)
	{
		return self::i()->setExtraCredentials($data);
	}

	static function hasCredentials()
	{
		return self::i()->hasCredentials();
	}

	static function clearCredentials()
	{
		return self::i()->clearCredentials();
	}

	public static function me($id)
	{
		$args = func_get_args();
		return call_user_func_array(array(self::i(), __FUNCTION__), $args);
	}

	static function setGateway($gateway)
	{
		self::$gateway = $gateway;
	}

	static function getGateway()
	{
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
			if (auth::i($authEngine)->getCredentials())
			{
				auth::setGateway($authEngine);
			}
		}

		if (!auth::getGateway())
		{
			if (is_array(conf::i()->application[APPLICATION]['auth']))
			{
				auth::setGateway(conf::i()->application[APPLICATION]['auth'][0]);
			}
			else
			{
				auth::setGateway('server');
			}
		}
		
	}

	static function role()
	{
		$role	=	auth::getExtraCredentials('role');
		return ($role ? $role : 'guest');
	}

}

