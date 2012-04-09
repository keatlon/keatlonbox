<?php
class js
{
	static $scripts = array();

	public static function get($value)
	{
		return json_encode($value);
	}

	public static function load($src)
	{
		self::$scripts[] = $src;
	}

	public static function renderScripts()
	{
		foreach (self::$scripts as $src)
		{
			echo sprintf('<script type="text/javascript" src="%s"></script>', $src);
		}
	}
}


