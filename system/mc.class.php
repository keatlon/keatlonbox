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
        $data['ttl']    = $ttl;
        $res = self::i()->set($key, $data, 0, $ttl);
    }

    static public function isEmpty($key)
    {
        return (bool)!self::i()->get($key);
    }

    static public function buildKey($ns, $params)
    {
        return self::$prefix . implode('::', $ns) . '_' . implode($params);
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

    static protected function i()
    {
        if (!self::$instance)
        {
            self::$instance = new Memcache();
        }

        return self::$instance;
    }

    static public function init()
    {
        foreach (conf::$conf['memcache'] as $memcacheConfig)
        {
            if (!$memcacheConfig['enabled'])
            {
                continue;
            }

            $res = self::i()->addServer(
                    $memcacheConfig['host'],
                    $memcacheConfig['port'],
                    $memcacheConfig['persistent'],
                    $memcacheConfig['weight'],
                    $memcacheConfig['timeout'],
                    $memcacheConfig['retry_interval'],
                    $memcacheConfig['status'],
                    $memcacheConfig['failure_callback']
            );
            self::$prefix   = $memcacheConfig['prefix'];
            self::$enabled  = true;
        }
    }

}
