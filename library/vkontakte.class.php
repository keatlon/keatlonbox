<?

class vkontakte
{
	static function id()
	{
		$session 	=	array();
		$member 	=	false;
		$validKeys	= 	array('expire', 'mid', 'secret', 'sid', 'sig');

		$appCookie = 	$_COOKIE['vk_app_' . conf::$conf['vkontakte']['id']];

		if ($appCookie)
		{
			$session_data = explode ('&', $appCookie, 10);

			foreach ($session_data as $pair)
			{
				list($key, $value) = explode('=', $pair, 2);
				if (empty($key) || empty($value) || !in_array($key, $validKeys))
				{
					continue;
				}
				$session[$key] = $value;
			}

			foreach ($validKeys as $key)
			{
				if (!isset($session[$key]))
				{
					return $member;
				}
			}

			ksort($session);

			$sign = '';

			foreach ($session as $key => $value)
			{
				if ($key != 'sig')
				{
					$sign .= ($key.'='.$value);
				}
			}

			$sign	.=	conf::$conf['vkontakte']['key'];
			$sign	=	md5($sign);

			if ($session['sig'] == $sign && $session['expire'] > time())
			{
				$member = array
				(
					'id' 		=>	intval($session['mid']),
					'secret' 	=>	$session['secret'],
					'sid' 		=>	$session['sid']
				);
			}
		}

		return $member['id'];
	}
}