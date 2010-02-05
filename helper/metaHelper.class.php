<?php
class metaHelper
{
	private static $title;
	
	function setTitle($title)
	{
		self::$title = $title;
	}

	function getTitle()
	{
		if (!self::$title)
		{
			return conf::i()->application[application::$name]['title'];
		}

		return self::$title;
	}
}
?>
