<?php
class log
{
    const E_EXCEPTION   = 10;
    const E_PHP         = 20;
    const E_USER        = 30;

	static $erorrs		= array();

    static public function init()
    {
		ini_set('display_errors', false);
		
		error_reporting(E_ALL & ~E_NOTICE);
        set_error_handler(array('log', 'php_error_handler'), E_ALL & ~E_NOTICE);
    }


    static public function exception(Exception $e)
    {
        if (!conf::$conf['debug']['log_exceptions'])
        {
            return true;
        }


		$output[]	=	get_class($e) . ': ' . $e->getMessage();
		$output[]	=	'';

		$trace	=	$e->getTrace();

		foreach ($trace as $traceItem)
		{
			$output[]	=	"\t" . $traceItem['file'] . '(line ' . $traceItem['line'] . ')';
			$output[]	=	"\t" . $traceItem['class'] . $traceItem['type'] . $traceItem['function'] . json_encode($traceItem['args']);
			$output[]	=	'';
		}

        log::push(implode("\n", $output), false, log::E_EXCEPTION);
    }

    static public function php_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
    {
		$message = $errstr . " in " . $errfile . " at line " . $errline;
		
		switch($errno)
		{
			case E_NOTICE:
				$message = 'NOTICE: ' . $message;
				break;

			case E_WARNING:
				break;
		}
		
		log::push($message, $errno, log::E_PHP);
    }

    public static function push($msg, $label = 'default', $type = log::E_USER)
    {
		if (conf::$conf['debug']['display_errors'])
		{
			$item['type']		= $type;
			$item['label']		= $label;
			$item['message']	= $msg;

			log::$erorrs[] = $item;
		}


		switch($type)
		{
			case log::E_PHP:

				if (!conf::$conf['debug']['log_errors'])
				{
					return false;
				}
				
				$filename = conf::$conf['debug']['log_errors'];

				break;

			case log::E_EXCEPTION:

				if (!conf::$conf['debug']['log_exceptions'])
				{
					return false;
				}

				$filename = conf::$conf['debug']['log_exceptions'];

				break;

			case log::E_USER:

				if (!conf::$conf['debug']['log_exceptions'])
				{
					return false;
				}

				$filename = conf::$conf['debug']['log_information'];

				break;
		}

        $fh = fopen($filename, 'a+');

		$line = array(
			"",
			'on ' . date('d-m-Y H:i:s') . '] ' . $_SERVER['REMOTE_ADDR'] . ':' . $_SERVER['REQUEST_URI'],
			$msg
		);

        fwrite($fh, implode("\n", $line) . "\n\n");
        fclose($fh);
    }

	public static function getTrace()
	{
		$debugTrace = debug_backtrace();

		foreach($debugTrace as $debugItem)
		{
			
		}
	}

}
