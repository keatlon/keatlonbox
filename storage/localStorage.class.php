<?php

class localStorage
{
	static protected $data = array();

	static function set($key, $value)
	{
		self::$data[$key]	=	$value;
	}

	static function get($key)
	{
		return self::$data[$key];
	}
}

?>
