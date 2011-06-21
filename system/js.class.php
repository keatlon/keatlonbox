<?php
class	js
{
	static private $commands	=	array();
	static private $vars		=	array();
	static private $context		=	'body';

	static function context($context = false)
	{
		if ($context)
		{
			self::$context	=	$context;
		}

        return self::$context;
	}

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

	static function init($selector, $plugin, $params = array(), $context = false)
	{
		self::$commands[]	=	array
		(
			'command'	=>	'init',
			'selector'	=>	$selector,
			'plugin'	=>	$plugin,
			'params'	=>	$params,
			'context'	=>	$context
		);
	}

	static function set($selector, $html, $context = false)
	{
		self::$commands[]	=	array(
			'command'	=>	'set',
			'selector'	=>	$selector,
			'html'		=>	$html,
			'context'	=>	$context
		);
	}

	static function append($selector, $html, $context = false)
	{
		self::$commands[]	=	array(
			'command'	=>	'append',
			'selector'	=>	$selector,
			'html'		=>	$html
		);
	}

	static function remove($selector, $context = false)
	{
		self::$commands[]	=	array(
			'command'	=>	'remove',
			'selector'	=>	$selector
		);
	}

	static function insert($selector, $html, $context = false)
	{
		self::$commands[]	=	array(
			'command'	=>	'insert',
			'selector'	=>	$selector,
			'html'		=>	$html
		);
	}
	
}
