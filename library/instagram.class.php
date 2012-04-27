<?php
class instagram
{
	/**
	 * @var OAuth
	 */
	static protected $instance = false;

	/**
	 *
	 * @return OAuth
	 */
	static function i()
	{
		if (!self::$instance)
		{
			self::$instance = new OAuth(conf::$conf['instagram']['key'], conf::$conf['instagram']['secret'], OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);
			self::$instance->disableSSLChecks();
			self::$instance->enableDebug();
		}

		return self::$instance;
	}
	
	protected static function array2uri($params)
	{
		$uriparams	=	array();

		foreach ($params as $key => $value)
		{
			$uriparams[] = $key . '=' . rawurlencode($value);
		}

		return implode('&', $uriparams);
	}

	static function getRequestToken($redirectUrl = false)
	{
		$redirectUrl = $redirectUrl ? $redirectUrl : conf::$conf['domains']['web'] . conf::$conf['instagram']['localAuthorizeUrl'];

		$params	=	array
		(
			'client_id'		=>	conf::$conf['instagram']['key'],
			'display'		=>	'touch',
			'redirect_uri'	=>	$redirectUrl,
			'response_type'	=>	'code'
		);

		return response::redirect(conf::$conf['instagram']['authorizeUrl'] . '/?' . self::array2uri($params));
	}

	static function getAccessToken($code, $redirectUrl = false)
	{
		$redirectUrl = $redirectUrl ? $redirectUrl : conf::$conf['domains']['web'] . conf::$conf['instagram']['localAuthorizeUrl'];

		$params	=	array
		(
			'client_id'		=>	conf::$conf['instagram']['key'],
			'client_secret'	=>	conf::$conf['instagram']['secret'],
			'redirect_uri'	=>	$redirectUrl,
			'grant_type'	=>	'authorization_code',
			'code'			=>	$code
		);

		$ch		=	curl_init(conf::$conf['instagram']['accessTokenUrl']);

		curl_setopt_array($ch, array(
			CURLOPT_CONNECTTIMEOUT	=>	10,
			CURLOPT_RETURNTRANSFER	=>	1,
			CURLOPT_TIMEOUT			=>	60,
			CURLOPT_POST			=>	1,
			CURLOPT_SSL_VERIFYPEER	=>	false,
			CURLOPT_SSL_VERIFYHOST	=>	false,
		));

		curl_setopt($ch, CURLOPT_POSTFIELDS, self::array2uri($params));
		$result = curl_exec($ch);
		curl_close($ch);

		if (!$result)
		{
			return false;
		}

		return json_decode($result, true);
	}

	static function getUser($userId = 'self', $accessToken = false)
	{
		$params	=	array
		(
			'access_token'	=>	$accessToken,
		);

		$ch		=	curl_init('https://api.instagram.com/v1/users/' . $userId . '?' . self::array2uri($params));

		curl_setopt_array($ch, array(
			CURLOPT_CONNECTTIMEOUT	=>	10,
			CURLOPT_RETURNTRANSFER	=>	1,
			CURLOPT_TIMEOUT			=>	60,
			CURLOPT_POST			=>	0,
			CURLOPT_SSL_VERIFYPEER	=>	false,
			CURLOPT_SSL_VERIFYHOST	=>	false,
		));

		$result = curl_exec($ch);
		curl_close($ch);

		if (!$result)
		{
			return false;
		}

		return json_decode($result, true);
	}



	static function getMedia($userId = 'self', $accessToken = false)
	{
		$params	=	array
		(
			'access_token'	=>	$accessToken,
		);

		$ch		=	curl_init('https://api.instagram.com/v1/users/' . $userId . '/media/recent?' . self::array2uri($params));

		curl_setopt_array($ch, array(
			CURLOPT_CONNECTTIMEOUT	=>	10,
			CURLOPT_RETURNTRANSFER	=>	1,
			CURLOPT_TIMEOUT			=>	60,
			CURLOPT_POST			=>	0,
			CURLOPT_SSL_VERIFYPEER	=>	false,
			CURLOPT_SSL_VERIFYHOST	=>	false,
		));

		$result = curl_exec($ch);
		curl_close($ch);

		if (!$result)
		{
			return false;
		}

		return json_decode($result, true);
	}

}

