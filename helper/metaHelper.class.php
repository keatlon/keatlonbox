<?php
class metaHelper
{
	private static $title;

	private static $properties;
	
	static function setTitle($title)
	{
		self::$title = $title;
	}

	static function getTitle()
	{
		if (!self::$title)
		{
			return conf::i()->application[application::$name]['title'];
		}

		return self::$title;
	}

	static function add($property, $content)
	{
		self::$properties[$property] = $content;
	}

	static function get()
	{
		return self::$properties;
	}

}
