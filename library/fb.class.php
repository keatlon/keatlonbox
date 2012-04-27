<?php


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
			require conf::$conf['rootdir'] . conf::$conf['facebook']['lib'] . '/facebook.php';

			self::$instance = new Facebook(array
			(
				'appId'		=>	conf::$conf['facebook']['id'],
				'secret'	=>	conf::$conf['facebook']['secret'],
				'cookie'	=>	conf::$conf['facebook']['cookie'],
				'domain'	=>	conf::$conf['facebook']['domain'],
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

	static function getLoginUrl($permissions = 'email', $url = false)
	{
		if (!$url)
		{
			$url	=	conf::$conf['domains']['web'] . '/account/signin';
		}

		return self::i()->getLoginUrl(array(
			'redirect_uri'	=>	$url,
			'scope'			=>	$permissions
		));
	}

	static function getLogoutUrl($url = false)
	{
		if (!$url)
		{
			$url	=	conf::$conf['domains']['web'] . '/account/signout';
		}
		
		return self::i()->getLogoutUrl(array(
			'next'	=>	$url,
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

	static function removeRequest($id, $uid)
	{
		return self::i()->api(
			'/' . $id . '_' . $uid . '/',
			'DELETE',
			array(
				'access_token'	=>	fb::getAppToken()
			));
	}

	static function getAppToken()
	{
		$url	= sprintf('https://graph.facebook.com/oauth/access_token?client_id=%s&client_secret=%s&grant_type=client_credentials',
			conf::$conf['facebook']['id'],
			conf::$conf['facebook']['secret']
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

