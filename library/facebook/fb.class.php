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

	static function getLoginUrl($permission = 'email')
	{
		return self::i()->getLoginUrl(array(
			'redirect_uri'	=>	conf::i()->domains['web'] . '/account/signin',
			'scope'			=>	$permissions
		));
	}

	static function getLogoutUrl()
	{
		return self::i()->getLogoutUrl(array(
			'next'	=>	conf::i()->domains['web'] . '/account/signout',
		));
	}

	static function friends()
	{
		return self::i()->api('/me/friends');
	}

	static function photo($facebookId)
	{
		return imageStorage::save('http://graph.facebook.com/' . $facebookId . '/picture?type=large');
	}
	
	static function init()
	{
	}

	static function removeRequest($id)
	{
		return self::i()->api(
			'/' . $id . '/',
			'DELETE',
			array(
				'access_token'	=>	conf::i()->facebook['token']
			));
	}

	static function getAppToken()
	{
		$url	= sprintf('https://graph.facebook.com/oauth/access_token?client_id=%s&client_secret=%s&grant_type=client_credentials',
			conf::i()->facebook['id'],  
			conf::i()->facebook['secret']
		);

		$ch		=	curl_init($url);
		
		curl_setopt_array($ch, array(
			CURLOPT_CONNECTTIMEOUT	=>	10,
			CURLOPT_RETURNTRANSFER	=>	1,
			CURLOPT_TIMEOUT			=>	60,
			CURLOPT_POST			=>	0,
			CURLOPT_USERAGENT		=>	'facebook-php-2.0',
			CURLOPT_SSL_VERIFYPEER	=>	false,
			CURLOPT_SSL_VERIFYHOST	=>	false,
		));
		
		$result = curl_exec($ch);
		curl_close($ch);

		list($key, $token) = explode('=', $result, 2);

		return $token;
	}

	static function getAccessToken()
	{
		return self::i()->getAccessToken();
	}
}

