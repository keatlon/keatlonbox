<?php
class profiler
{
	protected static $type = 'default';

	public static function type($type = false)
	{
		if ($type)
		{
			self::$type = $type;
		}

		return self::$type;
	}

	public static function start($forced = false)
	{
		if (function_exists('xhprof_enable') && (conf::$conf['profiler']['enabled'] || $forced))
		{
			xhprof_enable(XHPROF_FLAGS_MEMORY);
		}
	}

	public static function stop($forced = false)
	{
		if (function_exists('xhprof_disable') && (conf::$conf['profiler']['enabled'] || $forced))
		{
			$data	=	xhprof_disable();

			require_once conf::$conf['rootdir'] . "/lib/plugins/xhprof/xhprof_lib.php";
			require_once conf::$conf['rootdir'] . "/lib/plugins/xhprof/xhprof_runs.php";

			$runs 	=	new XHProfRuns_Default();
			$id		=	$runs->save_run($data, self::type());

			echo "---------------\n".
				 "Assuming you have set up the http based UI for \n".
				 "XHProf at some address, you can view run at \n".
				 "http://<xhprof-ui-address>/index.php?run=$id&source=" . self::type().
				 "---------------\n";

		}
	}

}
