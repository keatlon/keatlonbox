<?php
class cookie
{
    static function set($key, $value, $expire = false, $domain = false)
    {
		if (!$expire)
		{
			$expire = time() + 86400*14;
			$_COOKIE[$key] = $value;
		}

		if ($domain)
		{
			return setcookie($key, $value, $expire, '/', $domain);
		}
		else
		{
			return setcookie($key, $value, $expire, '/');
		}
    }

    static function get($key)
    {
        return $_COOKIE[$key];
    }

    static function clear($key = false)
    {
        self::set($key, "", time() - 3600);
    }

}
