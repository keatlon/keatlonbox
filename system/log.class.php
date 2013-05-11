<?php
class log
{
	static protected $handlers		= array(
        array('log', 'handler')
    );

    static public function init()
    {
        if (isset(conf::$conf['log']['handlers']))
        {
            self::$handlers	=	conf::$conf['log']['handlers'];
        }

        set_error_handler(array('log', 'php'), ini_get('error_reporting'));
    }

	static function getTraceInfo(Exception $e)
	{
        $trace  =   $e->getTrace();

        foreach($trace as $item)
        {
            $lines[] = "# line " . $item['line'] . "\t" . $item['class'] . $item['type'] . $item['function'] . "(" . json_encode($item['args']) . ")";
        }

        return implode("\n", $lines);
	}

    private static function push($message, $component, $level, $params = array())
    {
        $attributes =   array();

        if (!is_array($params))
        {
            $attributes['message']      =   $message . ' ' . $params;
            $attributes['env']          =   ENVIRONMENT;
            $attributes['app']          =   APPLICATION;
            $attributes['created']      =   time();
            $attributes['ip']           =   $_SERVER['SERVER_ADDR'];
            $attributes['host']         =   $_SERVER['SERVER_NAME'];
        }
        else
        {
            $attributes['message']      =   $message;
            $attributes['env']          =   isset($params['env']) ? $params['env'] : ENVIRONMENT;
            $attributes['app']          =   isset($params['app']) ? $params['app'] : APPLICATION;
            $attributes['component']    =   $component;
            $attributes['level']        =   $level;
            $attributes['created']      =   time();
            $attributes['ip']           =   $_SERVER['SERVER_ADDR'];
            $attributes['host']         =   $_SERVER['SERVER_NAME'];

            if ($params)
            {
                $attributes = array_merge($attributes, $params);
            }
        }

        if (self::$handlers) foreach(self::$handlers as $handler)
        {
            call_user_func($handler, $attributes);
        }

        return true;
    }

    static function custom($message, $attributes = array(), $component = 'system')
   	{
        return log::push($message, $component, $component, $attributes);
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

        file_put_contents($file, d($attributes, true) . "\n", FILE_APPEND);

        return true;
    }

}
