<?php
class mc
{
    protected static $instance  = false;
    protected static $enabled   = false;
    protected static $prefix    = false;

    static public function get($key)
    {
        if (!self::$enabled)
        {
            return NULL;
        }

        $data = self::i()->get($key);

        if (!$data)
        {
            return NULL;
        }
        
        return $data['v'];
    }

    static public function set($key, $value, $ttl = 0)
    {
        if (!self::$enabled)
        {
            return false;
        }

        $data['v']      = $value;

        return self::i()->set($key, $data, $ttl);
    }

    static public function isEmpty($key)
    {
        return (bool)!self::i()->get($key);
    }

    static public function flush()
    {
        if (!self::$enabled)
        {
            return false;
        }

        return self::i()->flush();
    }

    static public function delete($key)
    {
        if (!self::$enabled)
        {
            return false;
        }
        
        return self::i()->delete($key);
    }

    static function i()
    {
        if (!self::$instance)
        {
            self::$instance = new Memcached();
        }

        return self::$instance;
    }

    static public function init()
    {
        foreach (conf::$conf['memcache'] as $config)
        {
            if (!$config['enabled'])
            {
                continue;
            }

            self::i()->addServer(
                $config['host'],
                $config['port'],
                $config['weight']
            );

            self::$enabled  = true;
        }
    }

    static function error($err)
    {
        dd($err);
    }

}
