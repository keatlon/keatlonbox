<?php

class gdata
{
	static protected $instance = false;

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

	static function revoke($token)
	{
		return self::curl(conf::$conf['gdata']['oauth2_revoke_uri'] . '?token=' . $token);
	}

	static function token($code)
	{
		$response	=	json_decode(self::curl(conf::$conf['gdata']['oauth2_token_uri'], array
		(
			'code'			=>	$code,
			'client_id'		=> 	conf::$conf['gdata']['id'],
			'client_secret' => 	conf::$conf['gdata']['secret'],
			'redirect_uri'	=> 	conf::$conf['gdata']['redirect'],
			'grant_type'	=>	'authorization_code'
		)), true);

		return (!$response || $response['error']) ? false : $response;
	}

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



	static function api($url, $accessToken)
	{
		return self::curl($url . '?access_token=' . $accessToken);
	}

	static function post()
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