<?
class db
{
	/**
	 * @return PDOStatement
	 */
	public static function exec( $sql, $bind = array(), $connection_name = null )
	{
		foreach ( $bind as $key => $value )
		{
			if (is_array($value))
			{
				$sql	=	str_replace(':' . $key, implode(',', $value), $sql);
			}
		}

		$statement = dbConnection::get( $connection_name )->prepare($sql);

		foreach ( $bind as $key => $value )
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
				$statement->bindValue( ":{$key}", $value, PDO::PARAM_STR );
			}
		}
		
		$statement->execute();
		
		if ( $statement->errorCode() != '0000' )
		{
			$error = $statement->errorInfo();
			throw new dbException($error[2], $sql);
		}

		return $statement;
	}
	
	public static function scalar( $sql, $bind = array(), $connection_name = null )
	{
		$statement = self::exec( $sql, $bind, $connection_name );
		return $statement->fetch( pdo::FETCH_COLUMN );
	}
	
	public static function row( $sql, $bind = array(), $connection_name = null )
	{
		$statement = self::exec( $sql, $bind, $connection_name );
		
		return $statement->fetch( pdo::FETCH_ASSOC );
	}
	
	public static function rows( $sql, $bind = array(), $connection_name = null )
	{
		$statement = self::exec( $sql, $bind, $connection_name );
		return $statement->fetchAll( pdo::FETCH_ASSOC );
	}
	
	public static function cols( $sql, $bind = array(), $connection_name = null )
	{
		$statement = self::exec( $sql, $bind, $connection_name );
		return $statement->fetchAll( pdo::FETCH_COLUMN );
	}
	
	public static function lastId()
	{
		return dbConnection::get()->lastInsertId();
	}
}