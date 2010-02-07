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
		ini_set('log_errors', false);
		
		error_reporting(E_ALL & ~E_NOTICE);
        set_error_handler(array('log', 'php_error_handler'), E_ALL & ~E_NOTICE);
    }

    static public function exception(Exception $e)
    {
        if (!conf::i()->debug['log_exception'])
        {
            return true;
        }

        $message = get_class($e) . ' ' . $e->getMessage()
                .   "\n--------------------"
                .   "\n" . $e->getTraceAsString()
                .   "\n--------------------"
                .   "\nIP: " . $_SERVER['REMOTE_ADDR']
				.   "\nreques: " . $_SERVER['REQUEST_URI']
				.   "\nuserid: " . auth::getCredentials()
        ;

        log::push(log::E_EXCEPTION, false, $message);
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
		
		log::push(log::E_PHP, $errno, $message);
    }

    public static function push($type, $label, $msg = false)
    {
		if (conf::i()->debug['display_errors'])
		{
			$item['type']		= $type;
			$item['label']		= $label;
			$item['message']	= $msg;

			log::$erorrs[] = $item;
		}


		switch($type)
		{
			case log::E_PHP:

				if (!conf::i()->debug['log_errors'])
				{
					return false;
				}
				
				$filename = conf::i()->debug['log_errors'];

				break;

			case log::E_EXCEPTION:

				if (!conf::i()->debug['log_exceptions'])
				{
					return false;
				}

				$filename = conf::i()->debug['log_exceptions'];

				break;

			case log::E_USER:

				if (!conf::i()->debug['log_exceptions'])
				{
					return false;
				}

				$filename = conf::i()->debug['log_information'];

				break;
		}

        $fh = fopen($filename, 'a+');

		$line = array(
			date('d-m-Y H:i:s'),
			$type,
			$label,
			$msg
		);

        fwrite($fh, implode("\t", $line) . "\n");
		
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

?>
