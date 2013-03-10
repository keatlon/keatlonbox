<?

class dbConnection
{
	protected static $connections = array();

    static function reset()
    {
        foreach ( $databases = conf::$conf['database']['pool'] as $alias => $conf )
        {
            unset(self::$connections[$alias]);
        }
    }

	public static function create( $alias = null, $params = null )
	{
		if ( !$alias )
		{
			$alias = conf::$conf['database']['default_connection'];
		}
		
		if ( !$params )
		{
			$databases = conf::$conf['database']['pool'];
			if ( !$databases[$alias] )
			{
				throw new Exception('DB connection params for "' . $alias . '" absent in Configuration');
			}
			
			$params = $databases[$alias];
		}

		$params['port']	=	isset($params['port']) ? $params['port'] : 3306;
		
		$uri = "mysql:host={$params['host']};port={$params['port']}";
		
		if ( $params['dbname'] )
		{
			$uri .= ";dbname={$params['dbname']}";
		}

		try
		{
			$options	=	array(
				PDO::MYSQL_ATTR_INIT_COMMAND 		=> "SET NAMES utf8",
				PDO::MYSQL_ATTR_USE_BUFFERED_QUERY 	=> true,
				PDO::ATTR_ERRMODE					=>	PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_PERSISTENT 				=> isset($params['persistent']) ? true : false
			);

			self::$connections[$alias] = new PDO($uri, $params['user'], $params['password'], $options);

		}
		catch (PDOException $e)
		{
			if ($e->getCode() == '2002')
			{
				throw new dbConnectionException;
			}
		}

		return self::$connections[$alias];
	}
	
	/**
	 * @return PDO
	 */
	public static function get( $alias = null, $force_connect = true )
	{
		if ( !$alias )
		{
			$alias = conf::$conf['database']['default_connection'];
		}
		
		if (!isset(self::$connections[$alias]))
		{
			if ( $force_connect )
			{
				return self::create( $alias );
			}
			
			return false;
		}
		
		return self::$connections[$alias];
	}
}