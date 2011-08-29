<?php

class aql
{
	protected static $results	=	false;
	protected static $pattern	=	false;
	protected static $search	=	false;

	static function fetch($array, $pattern, $search = null)
	{
		self::$search	=	$search;
		self::$results	=	false;
		self::$pattern	=	self::prepare($pattern);

		aql::rfetch($array);

		return self::$results;
	}

	protected static function rfetch($list, $level = 1, $path = '', $pattern = '')
	{

		foreach($list as $key => $value)
		{
			$keypath		=	$path ? $path . '.' . $key : $key;

			if (in_array($level - 1, self::$pattern['asterisk']))
			{
				$keyPattern	=	$pattern ? $pattern . '.*': '*';
			}
			else
			{
				$keyPattern	=	$pattern ? $pattern . '.' . $key : $key;
			}

			if (!self::match($keyPattern, self::$pattern['value'], true))
			{
				continue;
			}
			
			if (!isset(self::$search) && (self::$pattern['length'] == $level) && self::match($keyPattern, self::$pattern['value']))
			{
				self::$results[]	=	$value;
				continue;
			}

			if (isset(self::$search) && (self::$pattern['length'] == $level))
			{
			}

			if (isset(self::$search) && (self::$pattern['length'] == $level) && self::match($keyPattern, self::$pattern['value']) && ($value == self::$search))
			{

				self::$results[]	=	$list;
				continue;
			}
			
			if (is_array($value))
			{
				aql::rfetch($list[$key], $level + 1, $keypath, $keyPattern);
			}
		}

		return false;
	}



	protected static function prepare($pattern)
	{
		$chunks		=	explode('.', $pattern);
		$asterisk	=	array();

		for( $l = 0; $l < count($chunks); $l++)
		{
			if ($chunks[$l] == '*')
			{
				$asterisk[]	=	$l;
			}
		}

		return array
		(
			'value'		=>	$pattern,
			'asterisk'	=>	$asterisk,
			'length'	=>	count($chunks)
		);
	}

	protected static function match($path, $pattern, $subpath = false)
	{
		if ($subpath && strpos($pattern, $path) === 0)
		{
			return true;
		}

		return (bool)($pattern == $path);
	}

}