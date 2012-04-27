<?php
class twitter
{
	/**
	 * @var OAuth
	 */
	static protected $instance = false;

	const 	RESPONSE_OK						=	200;

	const 	RESPONSE_BAD_REQUEST 			= 	400;
	const 	RESPONSE_UNAUTHORIZED 			=	401;
	const 	RESPONSE_FORBIDDEN 				= 	403;
	const 	RESPONSE_NOT_FOUND 				= 	404;
	const	RESPONSE_NOT_ACCEPTABLE			= 	406;

	const	RESPONSE_ENHANCE_YOUR_CALM		= 	420;
	const	RESPONSE_INTERNAL_SERVER_ERROR	= 	500;
	const	RESPONSE_BAD_GATEWAY			= 	502;
	const	RESPONSE_SERVICE_UNAVAILABLE	= 	503;

	/**
	 *
	 * @return OAuth
	 */
	static function i()
	{
		if (!self::$instance)
		{
			self::$instance = new OAuth(conf::$conf['twitter']['key'], conf::$conf['twitter']['secret'], OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
			self::$instance->disableSSLChecks();
			self::$instance->enableDebug();
		}

		return self::$instance;
	}
	

	static function getRequestToken()
	{
		try
		{
			$token = self::i()->getRequestToken(conf::$conf['twitter']['requestTokenUrl'], conf::$conf['domains']['web'] . conf::$conf['twitter']['localAuthorizeUrl']);
		}
		catch(Exception $e)
		{
			return false;
		}

		if ($token['oauth_token'])
		{
			session::set('twrtoken', $token);
			return $token;
		}

		return false;
	}

	static function setAccessToken($token)
	{
		session::set('twatoken', $token);
	}

	static function getAccessToken($token, $requestToken)
	{
		self::i()->setToken($token, $requestToken['oauth_token_secret']);

		try
		{
			$token	=	self::i()->getAccessToken(conf::$conf['twitter']['accessTokenUrl']);

			if ($token)
			{
				return $token;
			}
		}
		catch (OAuthException $e)
		{
			return false;
		}

		return false;
	}

	static function getProfile($userId, $twitterId)
	{
		$token			=	session::get('twatoken');

		if (!$token)
		{
			return false;
		}

		self::i()->setToken($token['oauth_token'], $token['oauth_token_secret']);
		self::i()->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);

		try
		{
			self::i()->fetch('https://api.twitter.com/1/users/show.json', array(
				'user_id'	=>	$twitterId
			), OAUTH_HTTP_METHOD_GET);

			return json_decode(self::i()->getLastResponse(), true);
		}
		catch(Exception $e)
		{
			log::exception($e);
			return false;
		}
	}

	static function post($userId, $message)
	{
		if (!$message)
		{
			return false;
		}

		$userTwitter	=	users::getTwitter($userId);
		$token			=	$userTwitter['token'];

		if (!$token)
		{
			return false;
		}

		self::i()->setToken($token['oauth_token'], $token['oauth_token_secret']);
		self::i()->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);

		try
		{
			self::i()->fetch('https://api.twitter.com/1/statuses/update.json', array(
				'status'		=> 	$message,
				'wrap_links'	=>	true
			), OAUTH_HTTP_METHOD_POST);
		}
		catch(Exception $e)
		{
			log::exception($e);
			$info = self::i()->getLastResponseInfo();

			if ($info)
			{
				return (int)$info['http_code'];
			}

			return false;
		}

		$info = self::i()->getLastResponseInfo();
		return (int)$info['http_code'];
	}

	static function id()
	{
		$token = session::get('twatoken');
		return ($token ? $token['user_id'] : false);
	}

}

