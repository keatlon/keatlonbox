<?

class dbConnection
{
	protected static $connections = array();
	
	public static function create( $alias = null, $params = null )
	{
		$log_id = ( conf::i()->debug ) ? profiler::start(profiler::SQL, $alias . ' DB connecting ', 'DB connection') : null;
		
		if ( !$alias )
		{
			$alias = conf::i()->database['default_connection'];
		}
		
		if ( !$params )
		{
			$databases = conf::i()->database['pool'];
			
			if ( !$databases[$alias] )
			{
				throw new Exception('DB connection params for "' . $alias . '" absent in Configiguration');
			}
			
			$params = $databases[$alias];
		}
		
		$uri = "mysql:host={$params['host']}";
		
		if ( $params['dbname'] )
		{
			$uri .= ";dbname={$params['dbname']}";
		}


		self::$connections[$alias] = new PDO($uri, $params['user'], $params['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true) );
		self::$connections[$alias]->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
		conf::i()->debug ? profiler::finish($log_id) : null;
		
		return self::$connections[$alias];
	}
	
	/**
	 * @return PDO
	 */
	public static function get( $alias = null, $force_connect = true )
	{
		if ( !$alias )
		{
			$alias = conf::i()->database['default_connection'];
		}
		
		if ( self::$connections[$alias] === null )
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