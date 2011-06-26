<?php

class aql
{
	const	SORT_ACSENDANT	=	1;
	const	SORT_DESCENDANT	=	2;


	static function get($list, $filters = array())
	{
		$params	=	array
		(
			'filters'	=>	$filters,
		);

		aql::walkGet($list, false, false, $params);

		/*
		 * clean modified lists
		 */
		foreach ($filters as $filter => $value)
		{
			if (is_int($filter))
			{
				$filter = $value;
			}

			$parts			=	explode('.', $filter);
								array_pop($parts);
			$removeFilter	=	implode('.', $parts);

			if ($removeFilter)
			{
				$list			=	aql::remove($list, array($removeFilter));
			}
			else
			{
				$list	=	array();
				break;
			}
		}

		/*
		 * apply filtered lists
		 */
		foreach ($params['results'] as $filter => $results)
		{
			foreach($results as $result)
			{
				if (!$list)
				{
					$list	=	$result;
				}
				else
				{
					$list	= array_merge_recursive($list, $result);
				}
			}
		}

		return $list;
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

	static function column($list, $filter)
	{
		$params	=	array
		(
			'filters'	=>	array($filter)
		);

		aql::walkColumn($list, false, false, $params);

		return $params['result'];
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

	static function walkColumn($list, $path, $fpath, &$params)
	{

		foreach($list as $key => $value)
		{
			$fullPath		=	$fpath ? $fpath . '.' . $key : $key;
			$currentPath	=	is_int($key) ? $path : ($path ? $path . '.' . $key : $key);

			if (is_array($value))
			{
				aql::walkColumn($list[$key], $currentPath, $fullPath, $params);
			}

			if ($params['filters']) foreach($params['filters'] as $filter)
			{
				if ($filter == $currentPath && $value)
				{
					$params['result'][]	=	$value;
				}
			}
		}

		return false;
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