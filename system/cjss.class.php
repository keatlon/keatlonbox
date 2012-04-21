<?php

class cjss
{
	const	CSS	=	'css';
	const	JS	=	'js';



	static function build($group, $type)
	{
		cjss::merge($group);

		if ($type == cjss::CSS)
		{
			cjss::less($group);
		}

		cjss::compress($group, $type);
	}

	static function process($filename)
	{
		$info = pathinfo($filename);

		switch($info['extension'])
		{
			case cjss::JS:
			case cjss::CSS:
				self::render($info['basename'], $info['extension']);
				break;

			default:
				die('Bad extension ' . $info['extension']);
				break;
		}
	}


	static function compress($group, $type)
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
	}

	static function less($group)
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

	static function merge($group)
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

	protected static function hasUpdates($group)
	{
		return	(bool)(self::lastCompiled($group) < self::lastTouched($group));
	}

	protected static function lastCompiled($group)
	{
		$meta 	= 	conf::i()->rootdir . conf::i()->cachedir . '/' . $group . '.meta';
		return 	file_exists($meta) ? file_get_contents($meta) : 0;
	}

	protected static function lastTouched($group)
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

	protected static function getStaticGroup($filename)
	{
		$info 	= 	pathinfo($filename);
		$parts	=	explode('.', $info['filename']);
		array_pop($parts);
		array_push($parts, $info['extension']);
		return implode('.', $parts);
	}

	protected static function getStaticFilename($group, $timestamp)
	{
		$info 	= 	pathinfo($group);
		return $info['filename'] . '.' . $timestamp . '.' . $info['extension'];
	}

	protected static function getFullStaticFilename($filename, $timestamp)
	{
		$out		=	conf::i()->rootdir . conf::i()->static['compiled'] . '/' . $filename;

		$info 	= 	pathinfo($filename);
		return $info['filename'] . '.' . $timestamp . '.' . $info['extension'];
	}

	static function render($filename, $type)
	{
		$group 		= 	self::getStaticGroup($filename);
		$rebuild	=	build::hasUpdates($group);

		switch($type)
		{
			case cjss::CSS:
				if ($rebuild)
				{
					build::css($group);
				}
				header('Content-type: text/css');
				break;

			case cjss::JS:
				if ($rebuild)
				{
					build::js($group);
				}
				header('Content-type: application/x-javascript');
				break;
		}


		echo file_get_contents(conf::i()->rootdir . '/web/static/' . $group);
	}

}