<?php
/*******************************************************************************
 * Redis PHP Bindings - http://code.google.com/p/redis/
 *
 * Copyright 2009 Ludovico Magnocavallo
 * Released under the same license as Redis.
 *
 * Version: 0.1
 *
 * $Revision$
 * $Date$
 *
 ******************************************************************************/


class redis
{
	protected $server;
	protected $port;
	protected $_sock;
	protected static $instance;

	/**
	 *	get instance of redis
	 *
	 *	@return redis
	 */
	static function i()
	{
		if (!self::$instance)
		{
			self::$instance = new redis(conf::i()->redis['host'], conf::i()->redis['port']);
		}

		return self::$instance;
	}

	function redis($host, $port=6379)
	{
		$this->host = $host;
		$this->port = $port;
	}

	/**
	 * Ping server
	 *
	 * @param int $server_index Index of the server
	 */
	public function ping( $server_index )
	{
		return $this->executeCommand($this->getConnection( $server_index ), 'PING');
	}

	# === Scalar operations ===

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public function set( $key, $value )
	{
		$key = conf::i()->redis['prefix'] . $key;

		$value = $this->packValue($value);
		$cmd = array("SET {$key} " . strlen($value), $value);

		$response = $this->executeCommand( $cmd );
		return $this->getError($response);
	}

	/**
	 * @param string $key
	 * @return mixed
	 */
	public function get( $key )
	{
		$key = conf::i()->redis['prefix'] . $key;

		$response = $this->executeCommand( "GET {$key}" );
		if ( $this->getError($response) )
		{
			return;
		}

		$length = (int)substr($response, 1);
		if ( $length > 0 )
		{
			$value = $this->getResponse();
			return $this->unpackValue($value);
		}
	}

	/**
	 * @param string $key
	 */
	public function delete( $key )
	{
		$key = conf::i()->redis['prefix'] . $key;
		return $this->executeCommand( "DEL {$key}" );
	}

	/**
	 * @param string $key
	 * @return boolean
	 */
	public function exists( $key )
	{
		$key = conf::i()->redis['prefix'] . $key;
		return $this->executeCommand( "EXISTS {$key}" ) == ':1';
	}

	/**
	 *
	 * @param string $key
	 * @param int $by
	 */
	public function inc( $key, $by = 1 )
	{
		$key = conf::i()->redis['prefix'] . $key;
		$response = $this->executeCommand( "INCRBY {$key} {$by}" );
		return substr($response, 1);
	}

	/**
	 *
	 * @param string $key
	 * @param int $by
	 */
	public function dec( $key, $by = 1 )
	{
		$key = conf::i()->redis['prefix'] . $key;
		$response = $this->executeCommand( "DECRBY {$key} {$by}" );
		return substr($response, 1);
	}

	# === List operations ===

	public function prepend( $key, $value )
	{
		$key = conf::i()->redis['prefix'] . $key;
		$value = $this->packValue($value);
		$cmd = array("LPUSH {$key} " . strlen($value), $value);

		$response = $this->executeCommand( $cmd );
		return $this->getError($response);
	}

	public function append( $key, $value )
	{
		$key = conf::i()->redis['prefix'] . $key;
		$value = $this->packValue($value);
		$cmd = array("RPUSH {$key} " . strlen($value), $value);

		$response = $this->executeCommand( $cmd );
		return $this->getError($response);
	}

	public function getList($key, $limit, $offset = 0)
	{
		$key = conf::i()->redis['prefix'] . $key;
		$limit--;
		$start = $offset;
		$end = $start + $limit;

		$response = $this->executeCommand( "LRANGE {$key} {$start} {$end}" );
		if ( $this->getError($response) )
		{
			return;
		}

		$count = (int)substr($response, 1);
		$list = array();
		for ( $i = 0; $i < $count; $i++ )
		{
			$length = substr($this->getResponse(), 1);
			$value = $this->getResponse();
			$list[] = $this->unpackValue($value);
		}

		return $list;
	}

	public function getFilteredList($key, $filters, $limit = 0, $offset = 0)
	{
		$key = conf::i()->redis['prefix'] . $key;
		$start = 0;
		$end = $this->getListLength($key);

		$response = $this->executeCommand( "LRANGE {$key} {$start} {$end}" );
		if ( $this->getError($response) )
		{
			return;
		}

		$limit = !$limit ? $end : $limit + $offset;

		$list = array();
		for ( $i = 0; $i < $end; $i++ )
		{
			$length = substr($this->getResponse(), 1);
			$value = $this->getResponse();
			$value = $this->unpackValue( $value );
			if ( ( $filters == array_intersect($value, $filters) ) && ( ++$added <= $limit ) )
			{
				$list[] = $value;
			}
		}

		$list = array_slice($list, $offset);

		return $list;
	}

	public function getListLength($key)
	{
		$key = conf::i()->redis['prefix'] . $key;
		$response = $this->executeCommand( "LLEN {$key}" );
		if ( $this->getError($response) )
		{
			return;
		}

		return (int)substr($response, 1);
	}

	public function removeFromList($key, $value, $count = 0)
	{
		$key = conf::i()->redis['prefix'] . $key;
		$value = $this->packValue($value);
		$response = $this->executeCommand( array("LREM {$key} {$count} " . strlen($value), $value) );

		if ( $this->getError($response) )
		{
			return;
		}

		return (int)substr($response, 1);
	}

	public function removeByFilter($key, $filters)
	{
		$key = conf::i()->redis['prefix'] . $key;
		$list = $this->getFilteredList($key, $filters);

		foreach ( $list as $item )
		{
			$this->removeFromList($key, $item);
		}
	}

	public function truncateList($key, $limit, $offset = 0)
	{
		$key = conf::i()->redis['prefix'] . $key;
		$limit--;
		$start = $offset;
		$end = $start + $limit;

		$response = $this->executeCommand( "LTRIM {$key} {$start} {$end}" );

		if ( $this->getError($response) )
		{
			return;
		}

		return true;
	}

	# === Set operations ===

	public function addMember( $key, $value )
	{
		$key = conf::i()->redis['prefix'] . $key;
		$value = $this->packValue($value);
		$cmd = array("SADD {$key} " . strlen($value), $value);

		$response = $this->executeCommand( $cmd );
		return $response == ':1';
	}

	public function removeMember( $key, $value )
	{
		$key = conf::i()->redis['prefix'] . $key;
		$value = $this->packValue($value);
		$cmd = array("SREM {$key} " . strlen($value), $value);

		$response = $this->executeCommand( $cmd );
		return $response == ':1';
	}

	public function isMember( $key, $value )
	{
		$key = conf::i()->redis['prefix'] . $key;
		$value = $this->packValue($value);
		$cmd = array("SISMEMBER {$key} " . strlen($value), $value);

		$response = $this->executeCommand( $cmd );
		return $response == ':1';
	}

	public function getMembers($key)
	{
		$key = conf::i()->redis['prefix'] . $key;
		$response = $this->executeCommand( "SMEMBERS {$key}" );
		if ( $this->getError($response) )
		{
			return;
		}

		$count = (int)substr($response, 1);
		$list = array();
		for ( $i = 0; $i < $count; $i++ )
		{
			$length = substr($this->getResponse(), 1);
			$value = $this->getResponse();
			$list[] = $this->unpackValue($value);
		}

		return $list;
	}

	public function getMembersCount($key)
	{
		$key = conf::i()->redis['prefix'] . $key;
		$response = $this->executeCommand( "SCARD {$key}" );
		if ( $this->getError($response) )
		{
			return;
		}

		return (int)substr($response, 1);
	}

	public function getIntersection($key1, $key2)
	{
		$response = $this->executeCommand( "SINTER {$key1},{$key2}" );
		if ( $this->getError($response) )
		{
			return;
		}

		$count = (int)substr($response, 1);
		$list = array();
		for ( $i = 0; $i < $count; $i++ )
		{
			$length = substr($this->getResponse(), 1);
			$value = $this->getResponse();
			$list[] = $this->unpackValue($value);
		}

		return $list;
	}

	public function getDiff($key1, $key2)
	{

		$response = $this->executeCommand( "SDIFF {$key1},{$key2}" );
		if ( $this->getError($response) )
		{
			return;
		}

		dd($response);

		$count = (int)substr($response, 1);
		$list = array();
		for ( $i = 0; $i < $count; $i++ )
		{
			$length = substr($this->getResponse(), 1);
			$value = $this->getResponse();
			$list[] = $this->unpackValue($value);
		}

		return $list;
	}


	# === Middle tier ===

	/**
	 * Init connection
	 */
	private function getConnection()
	{
		if ( !$this->handle )
		{
			if ( !$sock = fsockopen($this->host, $this->port, $errno, $errstr) )
			{
				return false;
			}

			$this->handle = $sock;
		}

		return $this->handle;
	}

	private function packValue( $value )
	{
		if ( is_numeric($value) )
		{
			return $value;
		}
		else
		{
			return serialize($value);
		}
	}

	private function unpackValue( $packed )
	{
		if ( is_numeric($packed) )
		{
			return $packed;
		}

		return unserialize($packed);
	}

	private function executeCommand( $commands )
	{
		$this->getConnection();
		if ( !$this->handle ) return false;

		if ( is_array($commands) )
		{
			$commands = implode("\r\n", $commands);
		}

		$command .= $commands . "\r\n";

		for ( $written = 0; $written < strlen($command); $written += $fwrite )
		{
			if ( !$fwrite = fwrite($this->handle, substr($command, $written)) )
			{
				return false;
			}
		}

		return $this->getResponse();
	}

	private function getResponse()
	{
		if ( !$this->handle ) return false;
		return trim(fgets($this->handle), "\r\n ");
	}

	private function getError( $response )
	{
		if ( strpos($response, '-ERR') === 0 )
		{
			return substr($response, 5);
		}

		return false;
	}
}   


?>
