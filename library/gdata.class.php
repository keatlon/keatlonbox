<?php

class gdata
{
	static protected $sessionKey 	= 'gdatatoken';
	static protected $instance 		= false;
	static protected $user 			= false;

	/**
	 * Get google user id
	 *
	 * @static
	 *
	 */
	static function user()
	{
		if (!self::$user)
		{
			self::$user = gdata::get('https://www.googleapis.com/oauth2/v1/userinfo', self::access());
		}

		return self::$user;
	}

	/**
	 * Refresh access token
	 *
	 * @static
	 * @param $token
	 * @return bool|mixed
	 */
	static function refresh($token)
	{
		$response	=	json_decode(self::curl(conf::$conf['gdata']['oauth2_token_uri'], array
		(
			'refresh_token'	=>	$token,
			'client_id'		=> 	conf::$conf['gdata']['id'],
			'client_secret' => 	conf::$conf['gdata']['secret'],
			'grant_type'	=>	'refresh_token'
		)), true);

		return (!$response || $response['error']) ? false : $response;
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
		self::curl(conf::$conf['gdata']['oauth2_revoke_uri'] . '?token=' . self::access());
		session::delete(self::$sessionKey);
	}

	static function access($id = false)
	{
		if ($id)
		{
			return userPeer::getMeta($id, 'access_token');
		}

		$token	=	session::get(self::$sessionKey);
		return $token['access_token'];
	}

	/**
	 * Get access token
	 *
	 * @static
	 * @param $code
	 * @return bool|mixed
	 */
	static function token($code = false)
	{
		if (!$code)
		{
			return session::get(self::$sessionKey);
		}

		$response	=	json_decode(self::curl(conf::$conf['gdata']['oauth2_token_uri'], array
		(
			'code'			=>	$code,
			'client_id'		=> 	conf::$conf['gdata']['id'],
			'client_secret' => 	conf::$conf['gdata']['secret'],
			'redirect_uri'	=> 	conf::$conf['gdata']['redirect'],
			'grant_type'	=>	'authorization_code'
		)), true);

		return (!$response || $response['error']) ? false : session::set(self::$sessionKey, $response);
	}

	/**
	 * Build google authorization URL
	 *
	 * @param bool $state
	 * @return string
	 */
	static function authorize($state = false)
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
	static function get($url, $access)
	{
		$response	=	self::curl($url . '?access_token=' . $access);
		$result 	= 	json_decode($response, true);

		if (!$result)
		{
			log::push(json_encode($result), 'GDATA');
			return false;
		}

		if ($result['error'])
		{
			log::push($response, 'GDATA');
			return false;
		}

		return $result;
	}

	static function post($url, $params, $token)
	{
	}

	static protected function curl($url, $post = array())
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

		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}

}