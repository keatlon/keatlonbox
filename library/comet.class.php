<?php
class comet
{
	static	$enabled	=	false;
	static	$push_url	=	false;

	static function init()
	{
		self::$enabled	= conf::i()->comet['enabled'];
		self::$push_url = conf::i()->comet['push_url'];

		session::set('cometRunHash', md5(rand(100, 10000)));
		conf::i()->comet['hash']	=	session::get('cometRunHash');
		staticHelper::javascript('cometSettings', conf::i()->comet, true);
	}

	static function push($channelId, $data)
	{
		if (!self::$enabled)
		{
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, self::$push_url . '?id=' . $channelId);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		$result = curl_exec($ch);
		curl_close($ch);
	}
}


?>
