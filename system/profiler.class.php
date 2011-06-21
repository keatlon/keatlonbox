<?php
class profiler
{
	const SYSTEM	= 1;
	const SQL       = 2;
	const CACHE     = 3;
	const USER      = 4;

	protected static $stack		= array();
	protected static $counter	= 0;
	protected static $firephp	= false;

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

		self::$stack[self::$counter] = $record;
		return self::$counter;
	}

	public static function finish($counter)
	{
		$record = self::$stack[$counter];
		$record['time'] = round(microtime(true) - $record['time'], 4);
		self::$stack[$counter] = $record;
        return $record['time'];
	}

	public static function dump()
	{
		// file_put_contents('c:/log', print_r(self::$stack, true), FILE_APPEND);
	}

	public static function get($type = false, $time = false)
	{
		if (!$type)
		{
			return self::$stack;
		}

		$r = array();
		
		foreach(self::$stack as $item)
		{
			if ($item['type'] == $type)
			{
				if (!$time)
				{
					$r[] = $item;
				}
				else
				{
					if ($item['time'] > $time)
					{
						$r[] = $item;
					}
				}
			}
		}

		return $r;

	}


  /**
   * Gets instance of FirePHP
   *
   * @return FirePHP
   */

	static function firephp()
	{
		if (!self::$firephp)
		{
			self::$firephp = FirePHP::getInstance(true);
		}

		return self::$firephp;
	}
}
