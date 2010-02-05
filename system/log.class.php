<?php
class log
{
    const E_EXCEPTION   = 10;
    const E_PHP         = 20;
    const E_USER        = 30;

    static public function init()
    {
        set_error_handler(array('log', 'php_error_handler'), E_ALL & ~E_NOTICE);
    }

    static public function info($info)
    {
        log::error(log::E_USER, $info);
    }

    static public function exception(Exception $e)
    {
        if (!conf::i()->log['exception'])
        {
            return true;
        }

        $message = get_class($e) . ' ' . $e->getMessage()
                .   "\n--------------------"
                .   "\n" . $e->getTraceAsString()
                .   "\n--------------------"
                .   log::getUserInfo()
                .   "\n";

        self::addMessage(conf::i()->log['exception'], $message);
    }

    static public function error($level, $message)
    {
        if (!conf::i()->log['errors'])
        {
            return true;
        }

        self::addMessage(conf::i()->log['errors'], $message);
    }

    static public function php_error_handler($errno, $errstr, $errfile, $errline, $errcontext)
    {
        log::error(log::E_PHP, $errstr . " in " . $errfile . " at line " . $errline);
    }

    static protected function addMessage($filename, $message)
    {
        $fh = fopen($filename, 'a+');
        fwrite($fh, date('d-m-Y H:i:s') . "\t" . $message . "\n");
        fclose($fh);
    }

    static public function getUserInfo()
    {
        return "\nIP: " . $_SERVER['REMOTE_ADDR']
        .   "\nREQUEST: " . $_SERVER['REQUEST_URI']
        .   "\nUSER_ID: " . auth::getCredentials()
        ;
    }

    public function sql($query, $params)
    {
		
	}

    public function log($msg = false, $label = false)
    {
        if (!$label)
        {
            return;
        }
        
        log::error(log::E_USER, $label . ':' . $msg);
    }

	public function getTrace()
	{
			$debugTrace = debug_backtrace();

			foreach($debugTrace as $debugItem)
			{
				
			}

	}

}

?>
