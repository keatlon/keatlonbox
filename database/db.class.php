<?
class db
{
	const READ 	= 1;
	const WRITE = 2;

    static $affected = 0;

	private static function getConnectionAlias( $connectionAlias, $type)
	{
		if (!$connectionAlias) switch($type)
		{
			case self::READ:
				return conf::$conf['database']['default_read_connection'];

			case self::WRITE:
				return conf::$conf['database']['default_write_connection'];
		}

		return $connectionAlias;
	}

	/**
	 * @return PDOStatement
	 */
	public static function exec( $sql, $bind = array(), $connectionAlias = null )
	{
		if ($bind) foreach ( $bind as $key => $value )
		{
			if (is_array($value))
			{
				foreach ($value as $valueItem)
				{
					if (is_string($valueItem))
					{
						$items[]	=	"'" . addslashes(self::_convert($valueItem)) . "'";
					}
					else
					{
						$items[]	=	$valueItem;
					}
				}

				$sql	=	str_replace(':' . $key, implode(',', $items), $sql);
			}
		}

		log::debug($sql, array(
            'bind'  =>  $bind,
            'alias' =>  $connectionAlias
        ), 'mysql');

		$statement = dbConnection::get( self::getConnectionAlias($connectionAlias, self::WRITE) )->prepare($sql);

		if ($bind) foreach ( $bind as $key => $value )
		{
			if (is_array($value))
			{
				continue;
			}

			if (is_int($value))
			{
				$statement->bindValue( ":{$key}", $value, PDO::PARAM_INT);
			}
			else
			{
				$statement->bindValue( ":{$key}", self::_convert($value), PDO::PARAM_STR );
			}
		}

		$statement->execute();
		return $statement;
	}
	
	public static function scalar( $sql, $bind = array(), $connectionAlias = null )
	{
		$statement = self::exec( $sql, $bind, self::getConnectionAlias($connectionAlias, self::READ) );
		return $statement->fetch( pdo::FETCH_COLUMN );
	}
	
	public static function row( $sql, $bind = array(), $connectionAlias = null )
	{
		$statement = self::exec( $sql, $bind, self::getConnectionAlias($connectionAlias, self::READ) );
		
		return $statement->fetch( pdo::FETCH_ASSOC );
	}
	
	public static function rows( $sql, $bind = array(), $connectionAlias = null )
	{
		$statement = self::exec( $sql, $bind, self::getConnectionAlias($connectionAlias, self::READ) );
		return $statement->fetchAll( pdo::FETCH_ASSOC );
	}
	
	public static function cols( $sql, $bind = array(), $connectionAlias = null )
	{
		$statement = self::exec( $sql, $bind, self::getConnectionAlias($connectionAlias, self::READ) );
		return $statement->fetchAll( pdo::FETCH_COLUMN );
	}

	public static function lastId($alias = false)
	{
		return dbConnection::get($alias)->lastInsertId();
	}

	public static function smart($query, $where, $bind, $connectionAlias = null)
	{
		$wherePlain	=	implode(" AND ", $where);
		$query		=	str_replace("[conditions]", $wherePlain, $query);
		return		db::rows($query, $bind, self::getConnectionAlias($connectionAlias, self::READ));
	}

	static function _convert($content)
	{
		if (!mb_check_encoding($content, 'UTF-8'))
		{
			return mb_convert_encoding($content, 'UTF-8');
		}

		return $content;
	}
}