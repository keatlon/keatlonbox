<?php

class head
{
	static protected $headers = array();

	static function render()
	{
		return implode("\n", self::$headers);
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
		}
		else
		{
			self::$headers[] = sprintf('<%s%s/>', $node, $plainAttrs);
		}
	}

	static function title($title)
	{
		self::add('title', false, $title);
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