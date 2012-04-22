<?php

class resource
{
	const	CSS	=	'css';
	const	JS	=	'js';

	protected static $css	=	array();
	protected static $js	=	array();

	static function load($type)
	{
		switch($type)
		{
			case self::JS:
				return implode("\n", self::$js);

			case self::CSS:
				return implode("\n", self::$css);
		}
	}

	static function add($group, $remote = false)
	{
		$info		=	pathinfo($group);
		$timestamp	= 	(conf::i()->static['check'] == 'modified') ? self::lastTouched($group) : self::lastCompiled($group);
		$href		=	$remote ? $remote : conf::i()->domains['static'] . '/static/' . self::getStaticFilename($group, $timestamp);

		switch($info['extension'])
		{
			case self::JS:
				self::$js[] = sprintf
				(
					'<script type="text/javascript" src="%s"></script>', $href
				);
				break;

			case self::CSS:
				self::$css[] = sprintf
				(
					'<link rel="stylesheet" href="%s" type="text/css" media="%s"/>', $href, 'screen'
				);
				break;
		}
	}

	static function build($group, $type)
	{
		self::merge($group);

		if ($type == self::CSS)
		{
			self::less($group);
		}

		return self::compress($group, $type);
	}


	static function render($group, $type)
	{
		if (self::hasUpdates($group))
		{
			$filename	=	self::build($group, $type);
		}

		switch($type)
		{
			case self::CSS:
				header('Content-type: text/css');
				break;

			case self::JS:
				header('Content-type: application/x-javascript');
				break;
		}


		echo file_get_contents($filename);
	}

	static function process($filename)
	{
		$info 	= 	pathinfo($filename);
		$group	=	self::getStaticGroup($filename);

		switch($info['extension'])
		{
			case self::JS:
			case self::CSS:
				self::render($group, $info['extension']);
				break;

			default:
				die('Bad extension ' . $info['extension']);
				break;
		}
	}

	protected static function cleanup($group, $max)
	{
		$basename	=	str_replace('.css', '.*\.css', $group);
		$basename	=	str_replace('.js', '.*\.js', $basename);
		$files 		=	scan(conf::i()->rootdir . conf::i()->static['compiled'], '|' . $basename . '|');

		foreach ($files as $file)
		{
			if (filemtime($file) < $max)
			{
				unlink($file);
			}
		}
	}

	static protected function compress($group, $type)
	{
		$touched	=	self::lastTouched($group);
		$filename	=	self::getStaticFilename($group, $touched);
		$in 		= 	conf::i()->rootdir . conf::i()->cachedir . '/' . $group . 'm';
		$out		=	conf::i()->rootdir . conf::i()->static['compiled'] . '/' . $filename;

		$cmd	=	sprintf
		(
			'%s -jar %s --type %s %s > %s',
			conf::i()->system['java'],
			conf::i()->static['yuicompressor'],
			$type,
			$in,
			$out
		);

		exec($cmd);
		unlink($in);
		file_put_contents(conf::i()->rootdir . conf::i()->cachedir . '/' . $group . '.meta', $touched);

		self::cleanup($group, $touched);

		return $out;
	}

	static protected function less($group)
	{
		require_once conf::i()->rootdir . conf::i()->lessphp['lib'] . '/lessc.inc.php';

		$in = $out	=	conf::i()->rootdir . conf::i()->cachedir . '/' . $group . 'm';
		$out	.=	'l';

		try
		{
			lessc::ccompile($in, $out);
		}
		catch (Exception $e) {
			die("Error: less cannot process the file");
		}

		unlink($in);
		rename($out, $in);
	}

	static protected function merge($group)
	{
		$conf 		= 	include conf::i()->rootdir . '/conf/' . PRODUCT . '.static.php';
		$content	=	'';

		foreach ($conf[$group] as $file)
		{
			if (!file_exists($file))
			{
				dd('Error: File ' . $file . ' does not exists in group ' . $group);
			}
			$content .= file_get_contents($file) . "\n";
		}

		file_put_contents( conf::i()->rootdir . conf::i()->cachedir . '/' . $group . 'm', $content);
	}

	static protected function hasUpdates($group)
	{
		return	(bool)(self::lastCompiled($group) < self::lastTouched($group));
	}

	static protected function lastCompiled($group)
	{
		$meta 	= 	conf::i()->rootdir . conf::i()->cachedir . '/' . $group . '.meta';
		return 	file_exists($meta) ? file_get_contents($meta) : 0;
	}

	static protected function lastTouched($group)
	{
		$conf 		= 	include conf::i()->rootdir . '/conf/' . PRODUCT . '.static.php';
		$last	=	0;

		foreach ($conf[$group] as $file)
		{
			if (!file_exists($file))
			{
				continue;
			}

			$current	=	filemtime($file);
			$last		=	($current > $last) ? $current : $last;
		}

		return $last;
	}

	static protected function getStaticGroup($filename)
	{
		$info 	= 	pathinfo($filename);
		$parts	=	explode('.', $info['filename']);
		array_pop($parts);
		array_push($parts, $info['extension']);
		return implode('.', $parts);
	}

	static protected function getStaticFilename($group, $timestamp)
	{
		$info 	= 	pathinfo($group);
		return $info['filename'] . '.' . $timestamp . '.' . $info['extension'];
	}

	static protected function getFullStaticFilename($filename, $timestamp)
	{
		$out		=	conf::i()->rootdir . conf::i()->static['compiled'] . '/' . $filename;

		$info 	= 	pathinfo($filename);
		return $info['filename'] . '.' . $timestamp . '.' . $info['extension'];
	}

}