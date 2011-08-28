<?php

class aql
{
	const	SORT_ACSENDANT	=	1;
	const	SORT_DESCENDANT	=	2;

	static function get($array, $patterns)
	{
		$matches	=	array();

		foreach ($patterns as $pattern => $value)
		{
			$results	=	aql::fetch($array, $pattern);

			if ($results)
			{
				foreach($results as $result)
				{
					if ($result	==	$value)
					{
						$matches[]	=	$result;
					}
				}
			}
		}

		return $matches;
	}

	static function remove($list, $filters)
	{
		$params	=	array
		(
			'filters'	=>	$filters,
		);
		
		aql::walkRemove($list, false, false, $params);

		return $list;
	}

	static function set($list, $key, $value)
	{

	}

	static function sort($key, $sort = aql::SORT_ACSENDANT)
	{

	}

	static function walkGet($list, $path, $fpath, &$params)
	{
		foreach($list as $key => $value)
		{
			$fullPath		=	$fpath ? $fpath . '.' . $key : $key;
			$currentPath	=	is_int($key) ? $path : ($path ? $path . '.' . $key : $key);

			if (is_array($value))
			{
				aql::walkGet($value, $currentPath, $fullPath, $params);
			}

			if ($params['filters']) foreach($params['filters'] as $filter => $condition)
			{
				if (is_int($filter) && $condition == $currentPath && $value)
				{
					$params['results'][$condition][]	=	aql::push($list, $fpath);
					continue;
				}

				if ($condition === false && $filter == $currentPath && $value === false)
				{
					$params['results'][$filter][]	=	aql::push($list, $fpath);
					continue;
				}

				if($filter == $currentPath)
				{
					if (is_array($condition))
					{
						if (in_array($value, $condition))
						{
							$params['results'][$filter][]	=	aql::push($list, $fpath);
							continue;
						}
					}
					elseif ($value == $condition)
					{
						$params['results'][$filter][]	=	aql::push($list, $fpath);
					}
				}
			}
						

		}

		return false;
	}


	static function walkRemove(&$list, $path, $fpath, &$params)
	{

		foreach($list as $key => $value)
		{
			$fullPath		=	$fpath ? $fpath . '.' . $key : $key;
			$currentPath	=	is_int($key) ? $path : ($path ? $path . '.' . $key : $key);

			if (is_array($value))
			{
				if (aql::walkRemove($list[$key], $currentPath, $fullPath, $params))
				{
					unset($list[$key]);
				}
			}

			if ($params['filters']) foreach($params['filters'] as $filter => $condition)
			{
				if (is_int($filter) && $condition == $fullPath && $value)
				{
					return true;
				}

				if($filter == $currentPath)
				{
					if (is_array($condition) && in_array($value, $condition))
					{
						return true;
					}

					if ($value == $condition)
					{
						return true;
					}
				}
			}
		}

		return false;
	}



	
	static function fetch($array, $pattern)
	{
		aql::rfetch($array, self::prepare($pattern), array(), $output);
		return $output['result'];
	}

	protected static function rfetch($list, $pattern, $path = array(), &$output = array())
	{
		foreach($list as $key => $value)
		{
			$keypath	= array_merge($path, array($key)) ;


			if (self::match($keypath, $pattern))
			{
				$output['result'][]	=	$value;
			}

			if (is_array($value))
			{
				aql::rfetch($list[$key], $pattern, $keypath, $output);
			}
		}

		return false;
	}



	protected static function prepare($pattern)
	{
		$chunks	=	explode('.', $pattern);

		return array
		(
			'chunks'	=>	$chunks,
			'length'	=>	count($chunks)
		);
	}

	protected static function match($path, $pattern)
	{

		if (count($path) != $pattern['length'])
		{
			return false;
		}

		$offset	=	0;
		
		foreach ($pattern['chunks'] as $chunk)
		{
			if ($chunk == '*')
			{
				$path[$offset]	=	'*';
			}

			$offset++;
		}

		return (bool)($pattern['chunks'] == $path);
	}

	static function push($value, $path, $level = 0)
	{
		$slices			=	explode('.', $path);
		$key			=	array_pop($slices);

		if ($key || $key === '0')
		{
			$element[$key]	=	$value;
		}
		else
		{
			$element	=	$value;
		}

		if($slices)
		{
			$path	=	implode('.', $slices);
			return aql::push($element, $path, ++$level);
		}
		else
		{
			return	$element;
		}
	}

}