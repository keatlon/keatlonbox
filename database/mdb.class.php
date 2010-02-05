<?php

class mdb
{
/**
 * @var Memcache
 */
	private static $handle;

	/**
	 * @return Memcache
	 */
	private static function getNative()
	{
		if ( !self::$handle )
		{
			$memcache = new Memcache;

			if ( !$memcache->connect(conf::i()->mdb['host'], conf::i()->mdb['port']) )
			{
				throw new applicationException('Cannot connect to mdb');
			}

			self::$handle = $memcache;
		}

		return self::$handle;
	}

	public static function set( $key, $value )
	{
		return self::getNative()->set($key, $value);
	}

	public static function inc( $key, $value = 1 )
	{
		return self::getNative()->increment($key, $value);
	}

	public static function get( $key, $flag = null )
	{
		return self::getNative()->get($key, $flag);
	}

	public static function del( $key )
	{
		return self::getNative()->delete( $key );
	}

	public static function listPush( $key, $value )
	{
		if ( ( $list = self::getNative()->get($key) ) === false )
		{
			self::set($key, array($value));
		}
		else
		{
			array_push($list, $value);
			self::set($key, $list);
		}
	}

	public static function listPop( $key )
	{
		if ( ( $list = self::getNative()->get($key) ) )
		{
			$value = array_push($list);
			self::set($key, $list);

			return $value;
		}
	}

	public static function flush()
	{
		return self::getNative()->flush();
	}

	public static function getStats()
	{
		return self::getNative()->getStats();
	}
}