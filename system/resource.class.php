<?php

class resource
{
	const	CSS		=	'css';
	const	JS		=	'js';

	const	BEFORE	=	'before';
	const	AFTER	=	'after';

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

	static function add($group, $remote = false, $type = false)
	{
		$info		=	pathinfo($group);
		$type		=	$type ? $type : $info['extension'];
		$timestamp	= 	(conf::$conf['static']['check'] == 'modified') ? self::lastTouched($group) : self::lastCompiled($group);
		$href		=	$remote ? $group : conf::$conf['domains']['static'] . '/static/' . self::getStaticFilename($group, $timestamp);

		switch($type)
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
					'<link rel="stylesheet" type="text/css" href="%s"/>', $href
				);
				break;
		}
	}

	static function build($group, $type)
	{
				self::merge($group);
				self::compile($group, $type);
				self::compress($group, $type);
		return	self::apply($group, $type);
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
				log::push('Bad extension ' . $info['extension'], 'resource', log::E_PHP);
				break;
		}
	}

	protected static function cleanup($group, $max)
	{
		$basename	=	str_replace('.css', '.*\.css', $group);
		$basename	=	str_replace('.js', '.*\.js', $basename);
		$files 		=	scan(conf::$conf['rootdir'] . conf::$conf['static']['compiled'], '|' . $basename . '|');

		foreach ($files as $file)
		{
			if (filemtime($file) < $max)
			{
				unlink($file);
			}
		}
	}

	static function apply($group, $type)
	{
		self::cleanup($group, $type);

		$touched	=	self::lastTouched($group);
		$in 		= 	conf::$conf['rootdir'] . conf::$conf['cachedir'] . '/' . $group . '.compressed';
		$out		=	conf::$conf['rootdir'] .
						conf::$conf['static']['compiled'] . '/' .
						self::getStaticFilename($group, $touched);

		copy($in, $out);

		file_put_contents
		(
			conf::$conf['rootdir'] . conf::$conf['cachedir'] . '/' . $group . '.meta',
			$touched
		);

		return $out;
	}

	static protected function compile($group, $type)
	{
		$in 	= 	conf::$conf['rootdir'] . conf::$conf['cachedir'] . '/' . $group . '.merged';
		$out	= 	conf::$conf['rootdir'] . conf::$conf['cachedir'] . '/' . $group . '.compiled';

		if (conf::$conf['static'][$type]['compile'])
		{
			// dd(sprintf(conf::$conf['static'][$type]['compiler'], $in, $out));
			$res	=	exec(sprintf(conf::$conf['static'][$type]['compiler'], $in, $out), $output, $return);

			if ($return)
			{
				log::push("Error during compiling " . $group, log::E_PHP);
				return false;
			}
		}
		else
		{
			copy($in, $out);
		}

		return true;
	}

	static protected function compress($group, $type)
	{
		$in 	= 	conf::$conf['rootdir'] . conf::$conf['cachedir'] . '/' . $group . '.compiled';
		$out	= 	conf::$conf['rootdir'] . conf::$conf['cachedir'] . '/' . $group . '.compressed';

		if (conf::$conf['static'][$type]['compress'])
		{
			exec(sprintf(conf::$conf['static'][$type]['compressor'], $in, $out));
		}
		else
		{
			copy($in, $out);
		}

		return true;
	}


	static protected function merge($group)
	{
		$conf 		= 	include conf::$conf['rootdir'] . '/conf/' . PRODUCT . '.static.php';
		$content	=	'';

		foreach ($conf[$group] as $file)
		{
			if (!file_exists($file))
			{
				$msg = 'Error: File ' . $file . ' does not exists in group ' . $group;
				log::push($msg, 'resource', log::E_PHP);
			}

			$content .= file_get_contents($file) . "\n";
		}

		file_put_contents(
			conf::$conf['rootdir'] . conf::$conf['cachedir'] . '/' . $group . '.merged',
			$content
		);
	}

	static protected function hasUpdates($group)
	{
		return	(bool)(self::lastCompiled($group) < self::lastTouched($group));
	}

	static protected function lastCompiled($group)
	{
		$meta 	= 	conf::$conf['rootdir'] . conf::$conf['cachedir'] . '/' . $group . '.meta';
		return 	file_exists($meta) ? file_get_contents($meta) : 0;
	}

	static protected function lastTouched($group)
	{
		$conf 		= 	include conf::$conf['rootdir'] . '/conf/' . PRODUCT . '.static.php';
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
		$out		=	conf::$conf['rootdir'] . conf::$conf['static']['compiled'] . '/' . $filename;

		$info 	= 	pathinfo($filename);
		return $info['filename'] . '.' . $timestamp . '.' . $info['extension'];
	}

}