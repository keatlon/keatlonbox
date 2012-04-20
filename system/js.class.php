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

	static function compile($group)
	{
		$conf 		= 	include CONFDIR . '/' . PRODUCT . '.static.php';
		$content	=	'';

		foreach ($conf[$group . '-js'] as $file)
		{
			if (!file_exists($file))
			{
				dd('File' . $file . ' does not exists in group ' . $group . '.js');
			}

			$content .= file_get_contents($file);
		}

		if ($content)
		{

			$input	=	conf::i()->rootdir . conf::i()->cachedir . '/' . $group . '.jsu';
			$output	=	conf::i()->rootdir . '/web/static/' . $group . '.js';

			$yui	=	'c:\webspace\yuicompressor.jar';
			file_put_contents( $input, $content);

			$cmd	=	sprintf('java -jar %s --type js %s > %s', $yui, $input, $output);
			exec($cmd);
		}
	}

	static function load()
	{
		return implode("\n", self::$scripts);
	}

	static function render($group)
	{
		js::compile($group);
		echo file_get_contents(conf::i()->rootdir . '/web/static/' . $group . '.js');
	}
}


