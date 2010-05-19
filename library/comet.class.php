<?php
class comet
{
	static	$enabled	=	true;
	static	$push_url	=	'http://cp.allthis.com/publish';


	static function init()
	{

	}

	static function push($channelId, $data)
	{
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
