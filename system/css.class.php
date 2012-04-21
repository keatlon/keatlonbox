<?php
class css
{
	static $scripts = array();

	static function add($src, $remote = false, $media = "screen")
	{
		$group		=	$src . '.css';
		$timestamp	= 	(conf::i()->static['check'] == 'modified') ? build::lastTouched($group) : build::lastCompiled($group);
		$href		=	$remote ? $remote : conf::i()->domains['static'] . '/static/' . $src . '.' . $timestamp . '.css';

		self::$scripts[] = sprintf
		(
			'<link rel="stylesheet" href="%s" type="text/css" media="%s"/>', $href, $media
		);
	}

	static function load()
	{
		return implode("\n", self::$scripts);
	}

}


