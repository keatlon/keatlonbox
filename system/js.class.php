<?php
class	js
{
	static private $commands	=	array();
	static private $vars		=	array();

	static function getCommands()
	{
        return self::$commands;
	}

	static function getVariables()
	{
        return self::$vars;
	}

	static function variable($name, $value)
	{
        self::$vars[$variable] = $value;
	}

	static static function init($selector, $plugin, $params)
	{
		self::$commands[]	=	array
		(
			'command'	=>	'init',
			'selector'	=>	$selector,
			'plugin'	=>	$plugin,
			'params'		=>	$params
		);
	}


	static function set($selector, $html)
	{
		self::$commands[]	=	array(
			'command'	=>	'set',
			'selector'	=>	$selector,
			'html'		=>	$html
		);
	}

	static function append($selector, $html)
	{
		self::$commands[]	=	array(
			'command'	=>	'append',
			'selector'	=>	$selector,
			'html'		=>	$html
		);
	}

	static function remove($selector)
	{
		self::$commands[]	=	array(
			'command'	=>	'remove',
			'selector'	=>	$selector
		);
	}

	static function replace($selector, $html)
	{
		self::$commands[]	=	array(
			'command'	=>	'replace',
			'selector'	=>	$selector,
			'html'		=>	$html
		);
	}

	static function insert($selector, $html)
	{
		self::$commands[]	=	array(
			'command'	=>	'insert',
			'selector'	=>	$selector,
			'html'		=>	$html
		);
	}
	
}
?>
