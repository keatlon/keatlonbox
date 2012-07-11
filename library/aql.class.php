<?php

class aql
{
	protected static $results	=	false;
	protected static $pattern	=	false;
	protected static $search	=	false;

	/**
	 * @static
	 * @param $field
	 * @param $mainList
	 * @param $mainKey
	 * @param $sourceList
	 * @param $sourceKey
	 * @return mixed
	 */
	static public function join($mainList, $extraList, $mainId, $extraId, $field)
    {
		$extraList = self::assoc($extraId, $extraList);

		foreach($mainList as &$mainItem)
		{
			$mainItem[$field] = $extraList[$mainItem[$mainId]];
		}

		return $mainList;
	}

	static public function assoc($key, $array)
    {
		if (!$array)
		{
			return array();
		}

		return @array_combine(self::cols($key, $array), $array);
	}

	static public function cols($key, $array)
	{
		$result = array();

		if (!is_array($array))
		{
			return $result;
		}

		foreach($array as $item)
		{
			$result[] = $item[$key];
		}

		return $result;
	}

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


	static function merge($a, $b)
	{
		foreach($b as $key => $value)
		{
			if(array_key_exists($key, $a) && is_array($value))
			{
				$a[$key] = self::merge($a[$key], $b[$key]);
			}
			else
			{
				$a[$key] = $value;
			}
		}

		return $a;
	}
}