<?php
class profiler
{
	const APPLICATION	= 0;
	const SYSTEM		= 1;
	const SQL       	= 2;
	const USER      	= 4;
	const RENDER		= 5;

	protected	static $appId		=	0;
	protected 	static $stack		= array();
	protected 	static $counters	= array();
	protected 	static $totals		= array();
	protected 	static $counter		= 0;
	protected 	static $firephp	=	 false;

	public static function start($type = self::SYSTEM, $message = false, $extra = false, $ts = false)
	{
		if ($type == self::SQL && is_array($extra))
		{
			foreach((array)$extra as $sqlParamKey => $sqlParamValue)
			{
				$message = str_replace(':' . $sqlParamKey, "'" . htmlspecialchars($sqlParamValue) . "'", $message);
			}
		}

		if (!$ts)
		{
			$ts = microtime(true);
		}

		self::$counter++;

		$record['time']		= $ts;
		$record['message']	= $message;
		$record['type']		= $type;

		self::$stack[$type][self::$counter] = $record;
		self::$counters[self::$counter]	= $type;

		if ($type == self::RENDER)
		{
			self::$appId = self::$counter;
		}

		return self::$counter;
	}

	public static function finish($counter)
	{
		$type	=	self::$counters[$counter];

		$record	= 	self::$stack[$type][$counter];

		$record['time'] = round(microtime(true) - $record['time'], 4);

		self::$totals[$type]	=	(float)self::$totals[$type] + $record['time'];
		self::$stack[$type][$counter] = $record;
        return $record['time'];
	}

	public static function total($type = false)
	{
		if ($type === false)
		{
			return self::$totals;
		}

		return self::$totals[$type];
	}

	public static function get($type = false)
	{
		if ($type === false)
		{
			return self::$stack;
		}

		return self::$stack[$type];
	}

	static function getAppId()
	{
		return self::$appId;
	}
}
