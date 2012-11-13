<?php
class profiler
{
	protected static $type		=	'default';
	protected static $started 	=	'default';

	public static function type($type = false)
	{
		if ($type)
		{
			self::$type = $type;
		}

		return self::$type;
	}

	static function umicrotime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return round(((float)$usec + (float)$sec) * 1000000);
	}

	public static function start()
	{
		if (!conf::$conf['profiler']['enabled'])
		{
			return false;
		}

		switch(conf::$conf['profiler']['type'])
		{
			case 'xhprof':
				xhprof_enable(XHPROF_FLAGS_MEMORY);
				break;

			case 'default':
			default:
				self::$started	=	self::umicrotime();
				break;
		}
	}

	public static function stop()
	{
		if (!conf::$conf['profiler']['enabled'])
		{
			return false;
		}

		switch(conf::$conf['profiler']['type'])
		{
			case 'xhprof':
				$data	=	xhprof_disable();

				require_once conf::$conf['rootdir'] . "/lib/plugins/xhprof/xhprof_lib.php";
				require_once conf::$conf['rootdir'] . "/lib/plugins/xhprof/xhprof_runs.php";

				$runs 	=	new XHProfRuns_Default();
				$id		=	$runs->save_run($data, self::type());
				break;

			case 'default':
			default:
				return (self::umicrotime() - self::$started);
				break;
		}
	}

}
