<?php
class js
{
	static $scripts = array();

	static function add($src, $remote = false)
	{
		self::$scripts[] = sprintf
		(
			'<script type="text/javascript" src="%s"></script>',
			$remote ? $remote : conf::i()->domains['static'] . '/static/' . $src . '.js'
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
			build::javascript($group);
		}

		header('Content-type: application/x-javascript');
		echo file_get_contents(conf::i()->rootdir . '/web/static/' . $group);
	}
}


