<?php
class profiler
{
	const SYSTEM	= 1;
	const SQL       = 2;
	const CACHE     = 3;
	const USER      = 4;

	protected static $stack = array();
	protected static $counter = 0;

	public static function start($type = self::SYSTEM, $message = false, $extra = false)
	{

		if ($type == self::SQL && is_array($extra))
		{
			foreach((array)$extra as $sqlParamKey => $sqlParamValue)
			{
				$message = str_replace(':' . $sqlParamKey, "'" . htmlspecialchars($sqlParamValue) . "'", $message);
			}
		}

		self::$counter++;
		$record['time'] = microtime(true);
		$record['message'] = $message;

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

	public static function get()
	{
		return self::$stack;
	}
}
?>
