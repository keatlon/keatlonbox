<?php
class css
{
	static $scripts = array();

	static function add($src, $remote = false)
	{
		self::$scripts[] = sprintf
		(
			'<link rel="stylesheet" href="%s" type="text/css" media="screen"/>',
			$remote ? $remote : conf::i()->domains['static'] . '/static/' . $src . '.css'
		);
	}

	static function load()
	{
		return implode("\n", self::$scripts);
	}

	static function compile($group)
	{
		$conf 		= 	include CONFDIR . '/' . PRODUCT . '.static.php';
		$content	=	'';

		foreach ($conf[$group . '-css'] as $file)
		{
			if (!file_exists($file))
			{
				dd('File' . $file . ' does not exists in group ' . $group . '.css');
			}

			$content .= file_get_contents($file) . "\n";
		}


		if ($content)
		{

			$lessinput	=	conf::i()->rootdir . conf::i()->cachedir . '/' . $group . '.lessu';
			$cssinput	=	conf::i()->rootdir . conf::i()->cachedir . '/' . $group . '.cssu';
			$cssoutput	=	conf::i()->rootdir . '/web/static/' . $group . '.css';

			$yui	=	'c:\webspace\yuicompressor.jar';
			file_put_contents( $lessinput, $content);

			require_once conf::i()->rootdir . conf::i()->lessphp['lib'] . '/lessc.inc.php';

			try
			{
				lessc::ccompile($lessinput, $cssinput);
			}
			catch (Exception $e)
			{
			}

			$cmd	=	sprintf('java -jar %s --type css %s > %s', $yui, $cssinput, $cssoutput);

			exec($cmd);
		}
	}

	static function render($group)
	{
		css::compile($group);
		echo file_get_contents(conf::i()->rootdir . '/web/static/' . $group . '.css');
	}
}


