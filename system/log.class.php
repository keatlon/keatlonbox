<?php
class log
{
	static protected $handler		= false;

    static public function init()
    {
		$reporting		=	conf::$conf['log']['error_reporting'] ? conf::$conf['log']['error_reporting'] : E_ALL & ~E_NOTICE;
		self::$handler	=	conf::$conf['log']['handler'] ? conf::$conf['log']['handler'] : false;

		error_reporting($reporting);
        set_error_handler(array('log', 'php'), $reporting);
    }

	static function getTraceInfo(Exception $e)
	{
		return "\n" . $e->getTraceAsString();
	}

    private static function push($message, $component, $level)
    {
		$ip		=	$_SERVER['REMOTE_ADDR'] ? $_SERVER['REMOTE_ADDR'] : 'console';
		$info	=	"[" . date('d M Y, H:i:s') . "] " . $_SERVER['REQUEST_URI'] . " (" . $ip . ")\n";

		if (self::$handler)
		{
			return call_user_func(self::$handler, $info . $message, $component, $level);
		}

		$prefix	=	conf::$conf['log']['prefix'] ? conf::$conf['log']['prefix'] : ENVIRONMENT;
		$file	=	conf::$conf['log']['path'] . '/' . $prefix . '.' . $component . '.' . $level . '.log';

		file_put_contents($file, $info . $message . "\n\n", FILE_APPEND);

		return true;
    }

	static function critical($message, $component = 'system')
	{
		if (conf::$conf['log']['critical'])
		{
			return log::push($message, $component, 'critical');
		}
	}

	static function error($message, $component = 'system')
	{
		if (conf::$conf['log']['error'])
		{
			return log::push($message, $component, 'error');
		}
	}

	static function warning($message, $component = 'system')
	{
		if (conf::$conf['log']['warning'])
		{
			return log::push($message, $component, 'warning');
		}
	}

	static function info($message, $component = 'system')
	{
		if (conf::$conf['log']['info'])
		{
			return log::push($message, $component, 'info');
		}
	}

	static function debug($message, $component = 'system')
	{
		if (conf::$conf['log']['debug'])
		{
			return log::push($message, $component, 'debug');
		}
	}

	static public function php($level, $error, $file, $line, $context)
	{
		switch($level)
		{
			case E_PARSE:
			case E_RECOVERABLE_ERROR:
				$level	=	'critical';
				break;

			case E_ERROR:
				$level	=	'error';
				break;

			case E_WARNING:
				$level	=	'warning';
				break;

			case E_STRICT:
			case E_DEPRECATED:
			case E_NOTICE:
				$level	=	'info';
				break;

			default:
				$level	=	'info';
				break;
		}

		$message = $error . "\nline $line in $file";
		return log::push($message, 'php', $level);
	}


}
