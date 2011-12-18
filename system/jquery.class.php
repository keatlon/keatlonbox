<?php
class	jquery
{
	const	INIT_SELF			=	1;
	const	INIT_CHILDREN		=	2;

	static private $commands	=	array();
	static private $vars		=	array();
	static private $selectors	=	array();

	static function init($selector, $init = jquery::INIT_CHILDREN)
	{
		self::$selectors[]	=	array('selector' => $selector, 'init' => $init);
	}

	static function getSelectors()
	{
		return self::$selectors;
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

	static function html($selector, $content, $init = jquery::INIT_CHILDREN)
	{
		self::$commands[] = array('command'     => 'html',
								  'selector'	=> $selector,
								  'html'		=> $content);

		if ($init)
		{
			jquery::init($selector, $init);
		}
	}

	static function render($selector, $template, $params, $init  = jquery::INIT_CHILDREN)
	{
		jquery::html($selector, partialHelper::render($template, $params, true), $init);
	}

	static function animate($selector, $method, $init  = jquery::INIT_SELF)
	{
		self::$commands[]	=	array(
			'command'	=>	'animate',
			'selector'	=>	$selector,
			'method'	=>	$method
		);

		if ($init)
		{
			jquery::init($selector, $init);
		}
	}

	static function append($selector, $template, $params, $init  = jquery::INIT_CHILDREN)
	{
		self::$commands[]	=	array(
			'command'	=>	'append',
			'selector'	=>	$selector,
			'html'		=>	partialHelper::render($template, $params, true)
		);
		
		if ($init)
		{
			jquery::init($selector, $init);
		}
	}

	static function prepend($selector, $template, $params, $init  = jquery::INIT_CHILDREN)
	{
		self::$commands[]	=	array(
			'command'	=>	'prepend',
			'selector'	=>	$selector,
			'html'		=>	partialHelper::render($template, $params, true)
		);

		if ($init)
		{
			jquery::init($selector, $init);
		}
	}

	static function replace($selector, $template, $params, $init  = jquery::INIT_CHILDREN)
	{
		self::$commands[]	=	array(
			'command'	=>	'replace',
			'selector'	=>	$selector,
			'html'		=>	partialHelper::render($template, $params, true)
		);

		if ($init)
		{
			jquery::init($selector, $init);
		}
	}

	static function remove($selector)
	{
		self::$commands[]	=	array(
			'command'	=>	'remove',
			'selector'	=>	$selector
		);
	}

	static function insert($selector, $template, $params, $init = jquery::INIT_CHILDREN)
	{
		self::$commands[]	=	array(
			'command'	=>	'insert',
			'selector'	=>	$selector,
			'html'		=>	partialHelper::render($template, $params, true),
		);

		if ($init)
		{
			jquery::init($selector, $init);
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

	static function hide($selector)
	{
		self::$commands[]	=	array(
			'command'	=>	'hide',
			'selector'	=>	$selector
		);
	}

	static function show($selector)
	{
		self::$commands[]	=	array(
			'command'	=>	'show',
			'selector'	=>	$selector
		);
	}
}
