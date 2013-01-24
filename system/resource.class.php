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
				log::error(sprintf('bad extension [%s]', $info['extension']), 'static');
				break;
		}
	}

	protected static function cleanup($group, $max)
	{
		$basename	=	str_replace('.css', '.*\.css', $group);
		$basename	=	str_replace('.js', '.*\.js', $basename);
		$files 		=	scan(ROOTDIR . conf::$conf['static']['compiled'], '|' . $basename . '|');

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
		$touched	=	self::lastTouched($group);
		$in 		= 	ROOTDIR . conf::$conf['cachedir'] . '/' . $group . '.compressed';
		$out		=	ROOTDIR .
						conf::$conf['static']['compiled'] . '/' .
						self::getStaticFilename($group, $touched);

		self::cleanup($group, $touched);

		copy($in, $out);

		file_put_contents
		(
            ROOTDIR . conf::$conf['cachedir'] . '/' . $group . '.meta',
			$touched
		);

		return $out;
	}

	static protected function compile($group, $type)
	{
		$in 	= 	ROOTDIR . conf::$conf['cachedir'] . '/' . $group . '.merged';
		$out	= 	ROOTDIR . conf::$conf['cachedir'] . '/' . $group . '.compiled';

		if (conf::$conf['static'][$type]['compile'])
		{
			$cmd	=	sprintf(conf::$conf['static'][$type]['compiler'], $in, $out);
			$res	=	exec($cmd, $output, $return);

			if ($return)
			{
				log::error(sprintf("Error during compiling %s %s", $group, $cmd), 'static');
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
		$in 	= 	ROOTDIR . conf::$conf['cachedir'] . '/' . $group . '.compiled';
		$out	= 	ROOTDIR . conf::$conf['cachedir'] . '/' . $group . '.compressed';

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
		$conf 		= 	include ROOTDIR . '/conf/' . PRODUCT . '.static.php';
		$content	=	'';

		foreach ($conf[$group] as $file)
		{
			if (!file_exists($file))
			{
				log::error(sprintf("File %s does not exists in group %s", $file, $group), 'static');
			}

			$content .= file_get_contents($file) . "\n";
		}

		file_put_contents(
            ROOTDIR . conf::$conf['cachedir'] . '/' . $group . '.merged',
			$content
		);
	}

	static protected function hasUpdates($group)
	{
		return	(bool)(self::lastCompiled($group) < self::lastTouched($group));
	}

	static protected function lastCompiled($group)
	{
		$meta 	= 	ROOTDIR . conf::$conf['cachedir'] . '/' . $group . '.meta';
		return 	file_exists($meta) ? file_get_contents($meta) : 0;
	}

	static protected function lastTouched($group)
	{
		$conf 		= 	include ROOTDIR . '/conf/' . PRODUCT . '.static.php';
		$last	=	0;

		if ($conf[$group]) foreach ($conf[$group] as $file)
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
		$info 	= 	pathinfo($filename);
		return $info['filename'] . '.' . $timestamp . '.' . $info['extension'];
	}

}