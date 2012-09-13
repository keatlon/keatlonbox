<?php

class head
{
	static protected $headers 	=	array();
	static protected $tags 		=	array();

	static function render()
	{
		return implode("\n", self::$headers);
	}

	static function get($node)
	{
		return	self::$tags[$node];
	}

	static function add($node, $attrs, $content = "")
	{
		$plainAttrs = '';

		if ($attrs)
		{
			foreach ($attrs as $key => $value)
			{
				$pairedAttrs[] = $key . '="' . $value . '"';
			}

			$plainAttrs = ' ' . implode(' ', $pairedAttrs) . ' ';
		}

		if ($content)
		{
			self::$headers[] = sprintf('<%s%s>%s</%s>', $node, $plainAttrs, h($content), $node);
			self::$tags[$node]	=	$content;
		}
		else
		{
			self::$headers[] = sprintf('<%s%s/>', $node, $plainAttrs);
			self::$tags[$node]	=	$attrs;
		}
	}

	static function title($title)
	{
		if (trim($title))
		{
			self::add('title', false, $title);
		}
	}

	static function meta($name, $content)
	{
		self::add('meta', array('name' => $name, 'content' => $content));
	}

	static function init()
	{
		self::add('meta', array
		(
			'http-equiv'	=>	'Content-Type',
			'content'		=>	'text/html; charset=utf-8'
		));
	}

}