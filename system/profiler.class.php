<?php
class profiler
{
	public static function start()
	{
		if (function_exists('xhprof_enable') && conf::$conf['profiler']['enabled'])
		{
			xhprof_enable(XHPROF_FLAGS_MEMORY);
		}
	}

	public static function stop()
	{
		if (function_exists('xhprof_disable') && conf::$conf['profiler']['enabled'])
		{
			$data	=	xhprof_disable();

			require_once conf::$conf['rootdir'] . "/lib/plugins/xhprof/xhprof_lib.php";
			require_once conf::$conf['rootdir'] . "/lib/plugins/xhprof/xhprof_runs.php";

			$runs = new XHProfRuns_Default();
			$run_id = $runs->save_run($data, "xhprof_foo");
		}
	}

}
