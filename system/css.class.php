<?php
class css
{
	static $scripts = array();

	static function add($src, $remote = false, $media = "screen")
	{
		$postfix 	= 	build::lastCompiled($src. '.css');
		$full		=	$remote ? $remote : conf::i()->domains['static'] . '/static/' . $src . '.css?' . $postfix;

		if (conf::i()->static['check'] && !$remote)
		{
			$full	=	conf::i()->domains['web'] . '/static.php?f=' . $src . '.css';
		}

		self::$scripts[] = sprintf
		(
			'<link rel="stylesheet" href="%s" type="text/css" media="%s"/>',
			$full,
			$media
		);
	}

	static function load()
	{
		return implode("\n", self::$scripts);
	}

	static function render($group)
	{
		if (!conf::i()->static['check'] || (conf::i()->static['check'] && build::hasUpdates($group)))
		{
			build::css($group);
		}

		header('Content-type: text/css');
		echo file_get_contents(conf::i()->rootdir . '/web/static/' . $group);
	}
}


