<?php
class comet
{
	static function init()
	{
		session::set('cometRunHash', md5(rand(100, 10000)));
		conf::$conf['comet']['hash']	=	session::get('cometRunHash');
	}

	static function push($channelId, $data)
	{
		if (!conf::$conf['comet']['enabled'])
		{
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, conf::$conf['comet']['push_url'] . '?id=' . $channelId);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		curl_close($ch);
	}
}
