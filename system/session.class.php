<?php

class session
{
    public static function init()
    {
        if (conf::$conf['application'][APPLICATION]['session'])
		{
			session::start();
		}
	}

    public static function start($sid = false)
    {
		if ($sid)
		{
			session_id($sid);
		}

        session_start();
        cookie::set(session_name(), session_id(), time() + 86400 * 14, conf::$conf['domain']['cookie'] );
    }

    public static function restart($sid)
    {
		session::destroy();
		session::start($sid);
	}

    public static function destroy()
    {
        cookie::clear(session_name());
        session_destroy();
    }

    public static function get($key)
    {
    	  return $_SESSION[$key];
    }

    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
}

class sessionHandler
{
	function open($save_path, $session_name)
	{
		global $sess_save_path;

		$sess_save_path = $save_path;
		return(true);
	}

	function close()
	{
		return(true);
	}

	function read($id)
	{
		global $sess_save_path;

		$sess_file = "$sess_save_path/sess_$id";
		return (string) @file_get_contents($sess_file);
	}

	function write($id, $sess_data)
	{
		global $sess_save_path;

		$sess_file = "$sess_save_path/sess_$id";
		if ($fp = @fopen($sess_file, "w")) {
			$return = fwrite($fp, $sess_data);
			fclose($fp);
			return $return;
		} else {
			return(false);
		}
	}

	function destroy($id)
	{
		global $sess_save_path;

		$sess_file = "$sess_save_path/sess_$id";
		return(@unlink($sess_file));
	}

	function gc($maxlifetime)
	{
		global $sess_save_path;

		foreach (glob("$sess_save_path/sess_*") as $filename) {
			if (filemtime($filename) + $maxlifetime < time()) {
				@unlink($filename);
			}
		}
		return true;
	}
}