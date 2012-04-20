<?php
class css
{
	static $scripts = array();

	static function add($src, $remote = false, $media = "screen")
	{
		self::$scripts[] = sprintf
		(
			'<link rel="stylesheet" href="%s" type="text/css" media="%s"/>',
			$remote ? $remote : conf::i()->domains['static'] . '/static/' . $src . '.css',
			$media
		);
	}

	static function load()
	{
		return implode("\n", self::$scripts);
	}

	static function render($group)
	{
		build::css($group);
		echo file_get_contents(conf::i()->rootdir . conf::i()->static['compiled'] . '/' . $group);
	}
}


