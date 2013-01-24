<?php
class log
{
	static protected $handler		= array('log', 'handler');

    static public function init()
    {
		$reporting		=	conf::$conf['log']['error_reporting'] ? conf::$conf['log']['error_reporting'] : E_ALL & ~E_NOTICE;

        if (conf::$conf['log']['handler'])
        {
            self::$handler	=	conf::$conf['log']['handler'];
        }

		error_reporting($reporting);
        set_error_handler(array('log', 'php'), $reporting);
    }

	static function getTraceInfo(Exception $e)
	{
		return "\n" . $e->getTraceAsString();
	}

    private static function push($message, $component, $level, $attributes = array())
    {
        $attributes['env']          =   ENVIRONMENT;
        $attributes['app']          =   APPLICATION;
        $attributes['component']    =   $component;
        $attributes['level']        =   $level;
        $attributes['created']      =   time();
        $attributes['ip']           =   $_SERVER['SERVER_ADDR'];
        $attributes['host']         =   $_SERVER['SERVER_NAME'];
        $attributes['message']      =   $message;

		return call_user_func(self::$handler, $attributes);
    }

	static function critical($message, $attributes = array(), $component = 'system')
	{
		if (conf::$conf['log']['critical'])
		{
			return log::push($message, $component, 'critical', $attributes);
		}
	}

	static function error($message, $attributes = array(), $component = 'system')
	{
		if (conf::$conf['log']['error'])
		{
			return log::push($message, $component, 'error', $attributes);
		}
	}

	static function warning($message, $attributes = array(), $component = 'system')
	{
		if (conf::$conf['log']['warning'])
		{
			return log::push($message, $component, 'warning', $attributes);
		}
	}

	static function info($message, $attributes = array(), $component = 'system')
	{
		if (conf::$conf['log']['info'])
		{
			return log::push($message, $component, 'info', $attributes);
		}
	}

	static function debug($message, $attributes = array(), $component = 'system')
	{
		if (conf::$conf['log']['debug'])
		{
			return log::push($message, $component, 'debug', $attributes);
		}
	}

	static public function php($level, $error, $file, $line, $context)
	{
        $message = $error . "\nline $line in $file";

		switch($level)
		{
			case E_PARSE:
			case E_RECOVERABLE_ERROR:
                return log::critical($message, array(), 'php');

			case E_ERROR:
                return log::error($message, array(), 'php');

			case E_WARNING:
                return log::warning($message, array(), 'php');

			case E_STRICT:
			case E_DEPRECATED:
                return log::info($message, array(), 'php');

            case E_NOTICE:
			default:
                return log::debug($message, array(), 'php');
		}

	}


    static function handler($attributes)
    {
        $file	=	conf::$conf['log']['path'] . '/' .  implode(".", array(
            $attributes['env'],
            $attributes['app'],
            $attributes['component'],
            $attributes['level'],
            'log'
        ));

        $attributes['created']  =   date('d M, Y H:i:s', $attributes['created']);

        file_put_contents($file, json_encode($attributes, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);

        return true;
    }

}
