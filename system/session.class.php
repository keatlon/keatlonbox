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

	public static function id()
	{
		return session_id();
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
		return $value;
    }

	public static function delete($key)
	{
		unset($_SESSION[$key]);
	}

}
