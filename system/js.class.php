<?php
class js
{
	static $scripts = array();

	static function add($src, $remote = false)
	{
		$group		=	$src . '.js';
		$timestamp	= 	(conf::i()->static['check'] == 'modified') ? build::lastTouched($group) : build::lastCompiled($group);
		$href		=	$remote ? $remote : conf::i()->domains['static'] . '/static/' . $src . '.' . $timestamp . '.js';

		self::$scripts[] = sprintf
		(
			'<script type="text/javascript" src="%s"></script>', $href
		);
	}

	static function load()
	{
		return implode("\n", self::$scripts);
	}

	static function render($filename)
	{
		$group = build::getStaticGroup($filename);

		if (build::hasUpdates($group))
		{
			build::javascript($group);
		}

		header('Content-type: application/x-javascript');
		echo file_get_contents(conf::i()->rootdir . '/web/static/' . $group);
	}


}


