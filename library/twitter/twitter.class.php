<?php
class twitter
{
	static protected $instance = false;

	/**
	 *
	 * @return OAuth
	 */
	static function i()
	{
		if (!self::$instance)
		{
			self::$instance = new OAuth(conf::i()->twitter['key'], conf::i()->twitter['secret'], OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
			self::$instance->disableSSLChecks();
			self::$instance->enableDebug();
		}

		return self::$instance;
	}
	

	static function getRequestToken()
	{
		$token = self::i()->getRequestToken(conf::i()->twitter['requestTokenUrl'], 'http://skillability.dev/twitter/login');
		if ($token['oauth_token'])
		{
			session::set('twrtoken', $token);
			return $token;
		}

		return false;
	}

	static function getAccessToken($userId, $token, $requestToken)
	{
		self::i()->setToken($token, $requestToken['oauth_token_secret']);

		try
		{
			$token	=	self::i()->getAccessToken(conf::i()->twitter['accessTokenUrl']);

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

}

