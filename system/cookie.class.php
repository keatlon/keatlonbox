<?php
class cookie
{
    static function set($key, $value, $expires = false, $domain = false)
    {
		if (!$expires)
		{
			$expires = time() + 86400*14;
			$_COOKIE[$key] = $value;
		}

		if ($domain)
		{
			return setcookie($key, $value, $expires, '/', $domain);
		}
		else
		{
			return setcookie($key, $value, $expires, '/');
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
