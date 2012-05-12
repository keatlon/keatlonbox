<?php

class gdata
{
	static protected $token 		= 	array();
	static protected $sessionKey 	= 	'gdatatoken';
	static protected $instance 		= 	false;

	static function init($accessToken = false, $refreshToken = false)
	{
	}

	static function isError($response, $info)
	{
		$json = json_decode($response, true);

		if ($info['http_code'] <> 200)
		{
			log::push($response, 'GDATA');
		}

		switch($info['http_code'])
		{
			case 200:
				if (!$json || $json['error'])
				{
					return true;
				}
				break;

			case 400:
				return true;

			case 401:
				if (strpos($response, 'NoLinkedYouTubeAccount') != false)
				{
					throw new gdataNoLinkedAccountException;
				}

				$newToken = gdata::refresh();

				if ($newToken)
				{
					throw new tokenRefreshedException($newToken);
				}

				throw new gdataBadRefreshTokenException;

				return true;

				break;

			case 403:
				break;

		}

		return false;
	}

	/**
	 * Refresh access token
	 *
	 * @static
	 * @param $token
	 * @return bool|mixed
	 */
	static function refresh($refreshToken = false)
	{
		if (!$refreshToken)
		{
			$refreshToken	=	self::getSessionToken('refresh_token');
		}

		$response		=	self::curl(conf::$conf['gdata']['oauth2_token_uri'], array
		(
			'refresh_token'	=>	$refreshToken,
			'client_id'		=> 	conf::$conf['gdata']['id'],
			'client_secret' => 	conf::$conf['gdata']['secret'],
			'grant_type'	=>	'refresh_token'
		), $info);

		if (self::isError($response, $info))
		{
			return false;
		}

		return json_decode($response, true);
	}

	/**
	 * Revoke access token
	 *
	 * @static
	 * @param $token
	 * @return mixed
	 */
	static function revoke()
	{
		self::curl(conf::$conf['gdata']['oauth2_revoke_uri'] . '?token=' . self::getSessionToken());
		session::delete(self::$sessionKey);
	}

	static function getSessionToken($key = 'access_token')
	{
		$token = session::get(self::$sessionKey);
		return ($key) ? $token[$key] : $token;
	}

	static function setSessionToken($token)
	{
		return session::set(self::$sessionKey, $token);
	}

	/**
	 * Get access token
	 *
	 * @static
	 * @param $code
	 * @return bool|mixed
	 */
	static function createAccessToken($code)
	{
		$response	=	self::curl(conf::$conf['gdata']['oauth2_token_uri'], array
		(
			'code'			=>	$code,
			'client_id'		=> 	conf::$conf['gdata']['id'],
			'client_secret' => 	conf::$conf['gdata']['secret'],
			'redirect_uri'	=> 	conf::$conf['gdata']['redirect'],
			'grant_type'	=>	'authorization_code'
		), $info);

		if (self::isError($response, $info))
		{
			return false;
		}

		return self::setSessionToken(json_decode($response, true));
	}

	/**
	 * Build google authorization URL
	 *
	 * @param bool $state
	 * @return string
	 */
	static function createAuthorizeUrl($state = false)
	{
		$params = array('response_type=code',
			'redirect_uri=' 	. urlencode(conf::$conf['gdata']['redirect']),
			'client_id=' 		. urlencode(conf::$conf['gdata']['id']),
			'scope=' 			. urlencode(conf::$conf['gdata']['scope']),
			'access_type=' 		. urlencode(conf::$conf['gdata']['access']),
			'approval_prompt=' 	. urlencode(conf::$conf['gdata']['promt']));

		if ($state)
		{
			$params[] = 'state=' . urlencode($state);
		}

		return conf::$conf['gdata']['oauth2_auth_url'] . "?" . implode('&', $params);
	}


	/**
	 * Execute api call
	 *
	 * @static
	 * @param $url
	 * @param $accessToken
	 * @return mixed
	 */
	static function get($url, $params = array())
	{
		if(!self::getSessionToken())
		{
			return false;
		}

		$params['access_token'] = 	self::getSessionToken();
		$params['alt'] 			= 	'json';
		$params['v'] 			= 	'2';

		foreach($params as $key => $value)
		{
			$rawParams[] = $key . '=' . $value;
		}

		$url 		=	$url . '?' . implode('&', $rawParams);
		$response	=	self::curl($url, array(), $info);

		if (self::isError($response, $info))
		{
			return false;
		}

		$json	= json_decode($response, true);

		return $json;
	}

	static function post($url, $params, $token)
	{
	}

	static protected function curl($url, $post = array(), &$info = false)
	{
		$curl	=	curl_init($url);
		$params	=	array
		(
			CURLOPT_CONNECTTIMEOUT     => 10,
			CURLOPT_RETURNTRANSFER     => 1,
			CURLOPT_TIMEOUT            => 60,
			CURLOPT_POST               => 0,
			CURLOPT_SSL_VERIFYPEER     => false,
			CURLOPT_SSL_VERIFYHOST     => false,
		);

		if ($post)
		{
			$params[CURLOPT_POST]		=	true;
			$params[CURLOPT_POSTFIELDS]	=	$post;
		}

		curl_setopt_array($curl, $params);

		$response 	= 	curl_exec($curl);
		$info		=	curl_getinfo($curl);

		curl_close($curl);
		return $response;
	}

}