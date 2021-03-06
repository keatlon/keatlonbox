<?php

class redis
{
	protected static $instances;

    static function reset()
    {
        self::$instances = false;
    }

	/**
	 *	get instance of redis
	 *
	 *	@return Predis\Client
	 */
	static function i($connection = 'default')
	{
		if (!self::$instances[$connection])
		{
			require_once ROOTDIR . '/lib/plugins/redis/Predis/Autoloader.php';
			Predis\Autoloader::register();

			if (!conf::$conf['redis']['pool'][$connection])
			{
				$host	=	conf::$conf['redis']['host'];
    			$port   =	conf::$conf['redis']['port'];
			}
			else
			{
				$host	=	conf::$conf['redis']['pool'][$connection]['host'];
    			$port   =	conf::$conf['redis']['pool'][$connection]['port'];
			}

            $extra  =   array();

            if (conf::$conf['redis']['prefix'])
            {
                $extra['prefix'] = conf::$conf['redis']['prefix'] . ':';
            }

			self::$instances[$connection] =	new Predis\Client(array(
			    'host'   				=>	$host,
			    'port'   				=>	$port,
				'connection_persistent'	=>	true,
			), $extra);
		}

		return self::$instances[$connection];
	}

	static function set($key, $value, $ttl = 0)
	{
		self::i()->set($key, $value);

        if ($ttl)
        {
            self::i()->expire($key, $ttl);
        }
	}

	static function get($key)
	{
		return self::i()->get($key);
	}

	static function inc($key, $inc = 1)
	{
		return self::i()->incrby($key, $inc);
	}

	static function dec($key, $inc = 1)
	{
		return self::i()->decrby($key, $inc);
	}

	static function del($key)
	{
		return self::i()->del($key);
	}

    static function subscribe($channel)
    {
        $handle     =   redis::i()->pubSub();
        $handle->subscribe($channel);
        return $handle;
    }

    static function psubscribe($pattern)
    {
        $handle     =   redis::i()->pubSub();
        $handle->psubscribe($pattern);
        return $handle;
    }

    static function unsubscribe($channel)
    {
        $handle     =   redis::i()->pubSub();
        $handle->unsubscribe($channel);
        return $handle;
    }

    static function listen($handle, $attributes = array(), $messagedCallback, $subscribedCallback = "", $unsubscribedCallback = "")
    {
        foreach ($handle as $message)
        {
            echo $message->kind . "\n";

            switch($message->kind)
            {
                case 'psubscribe':
                case 'subscribe':
                    if ($subscribedCallback)
                    {
                        $subscribedCallback($message->channel, $message->payload, $attributes, $handle);
                    }
                    break;

                case 'unsubscribe':
                    if ($unsubscribedCallback)
                    {
                        $unsubscribedCallback($message->channel, $message->payload, $attributes, $handle);
                    }
                    return true;
                    break;

                case 'message':
                    if ($messagedCallback)
                    {
                        $messagedCallback($message->channel, $message->payload, $attributes, $handle);
                    }
                    break;
            }
        }
    }

}
