<?php
class	js
{

	const	INIT_SELF			=	1;
	const	INIT_CHILDREN		=	2;

	static private $commands	=	array();
	static private $vars		=	array();
	static private $contexts	=	array();

	static function context($selector, $init = js::INIT_CHILDREN)
	{
		self::$contexts[]	=	array('context' => $selector, 'init' => $init);
	}

	static function getContexts()
	{
		return self::$contexts;
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
        self::$vars[$name] = $value;
	}

	static function init($selector, $plugin, $params = array(), $context  = js::INIT_CHILDREN)
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

	static function set($selector, $template, $params, $init  = js::INIT_CHILDREN)
	{
		self::$commands[]	=	array(
			'command'	=>	'set',
			'selector'	=>	$selector,
			'html'		=>	partialHelper::render($template, $params, true)
		);

		if ($init)
		{
			js::context($selector, $init);
		}
	}

	static function animate($selector, $method, $init  = js::INIT_SELF)
	{
		self::$commands[]	=	array(
			'command'	=>	'animate',
			'selector'	=>	$selector,
			'method'	=>	$method
		);

		if ($init)
		{
			js::context($selector, $init);
		}
	}

	static function append($selector, $template, $params, $init  = js::INIT_CHILDREN)
	{
		self::$commands[]	=	array(
			'command'	=>	'append',
			'selector'	=>	$selector,
			'html'		=>	partialHelper::render($template, $params, true)
		);
		
		if ($init)
		{
			js::context($selector, $init);
		}
	}

	static function remove($selector)
	{
		self::$commands[]	=	array(
			'command'	=>	'remove',
			'selector'	=>	$selector
		);
	}

	static function insert($selector, $template, $params, $init = js::INIT_CHILDREN)
	{
		self::$commands[]	=	array(
			'command'	=>	'insert',
			'selector'	=>	$selector,
			'html'		=>	partialHelper::render($template, $params, true),
		);

		if ($init)
		{
			js::context($selector, $init);
		}
	}

	static function raw($command)
	{
		self::$commands[]	=	array(
			'command'	=>	'raw',
			'value'		=>	$command
		);
	}

	static function attr($selector, $attribute, $value)
	{
		self::$commands[]	=	array(
			'command'	=>	'attr',
			'selector'	=>	$selector,
			'attr'		=>	$attribute,
			'value'		=>	$value
		);
	}

}
