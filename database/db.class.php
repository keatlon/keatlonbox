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
				foreach ($value as $valueItem)
				{
					if (is_string($valueItem))
					{
						$items[]	=	"'" . self::_convert($valueItem) . "'";
					}
					else
					{
						$items[]	=	$valueItem;
					}
				}

				$sql	=	str_replace(':' . $key, implode(',', $items), $sql);
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
				$statement->bindValue( ":{$key}", self::_convert($value), PDO::PARAM_STR );
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

	public static function smart($query, $where, $bind, $connection = null)
	{
		$wherePlain	=	implode(" AND ", $where);
		$query		=	str_replace("[conditions]", $wherePlain, $query);
		return		db::rows($query, $bind, $connection);
	}

	static function _convert($content)
	{
		if (!mb_check_encoding($content, 'UTF-8') OR !($content === mb_convert_encoding(mb_convert_encoding($content, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32')))
		{
			$content = mb_convert_encoding($content, 'UTF-8');
		}

		return $content;
	}
}