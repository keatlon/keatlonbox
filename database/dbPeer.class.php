<?

abstract class dbPeer
{

	protected static $instances = array();

	/**
	 * @static
	 * @param $className
	 * @return dbPeer
	 */
	static public function getInstance($className)
	{
		if (!self::$instances[$className])
		{
			self::$instances[$className] = new $className;
		}

		return self::$instances[$className];
	}


	/**
	 * get full row from table
	 *
	 * @param integer $primaryKey
	 * @return array
	 */
	public function doGetItem($primaryKey)
	{
		if (is_array($primaryKey))
		{
			$primaryKey	=	$primaryKey[0];
		}

		if (!$primaryKey)
		{
			return false;
		}

		return db::row('SELECT * FROM ' . $this->tableName . " WHERE {$this->primaryBind} LIMIT 1", $this->doBindPrimaryKey($primaryKey), $this->connectionName);
	}

	public function doGetItems($primaryKeys)
	{
		if (!is_array($primaryKeys) || !$primaryKeys)
		{
			return array();
		}

		foreach ($primaryKeys as $primaryKey)
		{
			$result[] = $this->doGetItem($primaryKey);
		}

		return $result;
	}

	/**
	 * get array of primary keys
	 *
	 * @param array $where
	 * @param array $bind
	 * @param array $order
	 * @param array $limit
	 * @return array
	 */
	public function doGetList($where = array(), $order = array(), $limit = false, $offset = false, &$total = false, &$more = false)
	{
		$bind			= array();
		$where_clause	= array();
		$fromTables[]	= $this->tableName;

		if (is_array($where))
			foreach ($where as $key => $value)
			{
				$operand	=	false;
				
				if (strpos($key, ' ') !== false)
				{
					list($key, $operand)	=	explode(' ', $key);
					$operand				=	strtolower($operand);
				}

				$bindKey = str_replace('.', '_', $key);
				switch ($operand)
				{
					case 'in':
						$where_clause[] = "{$key} IN (:{$bindKey})";
						break;
					
					case '!=':
						$where_clause[] = "{$key} <> :{$bindKey}";
						break;

					case '>':
						$where_clause[] = "{$key} > :{$bindKey}";
						break;

					case '>=':
						$where_clause[] = "{$key} >= :{$bindKey}";
						break;

					case '<':
						$where_clause[] = "{$key} < :{$bindKey}";
						break;

					case '<=':
						$where_clause[] = "{$key} <= :{$bindKey}";
						break;

					case '%':
						$where_clause[] = "{$key} LIKE :{$bindKey}";
						$value = '%' . $value . '%';
						break;

					default:
						$where_clause[] = "{$key} = :{$bindKey}";
				}


				$bind[$bindKey] = $value;
			}

		if (!$order)
		{
			$order = $this->primaryOKey;
		}

		$where_sql = implode(' AND ', $where_clause);
		$order_sql = implode(', ', $order);

		if ($limit && $offset)
		{
			$limit_sql = $offset . ', ' . $limit;
		} else if ($limit)
		{
			$limit_sql = $limit;
		}

		/**
		 * GET TOTAL ROWS QUERY
		 */
		$countSql = 'SELECT COUNT(' . $this->tableName . '.' . $this->primaryKey[0] . ') cnt ' . ' FROM ' . implode(',', $fromTables) .
			( $where_sql ? ' WHERE ' . $where_sql : '' );

		$countRow	= 	db::row($countSql, $bind, $this->connectionName);
		$total		= 	$countRow['cnt'];
		$more		=	$total > ((int)$offset + (int)$limit);

		/**
		 * GET ROWS QUERY
		 */
		$sql =	' SELECT ' . implode(',', $this->primaryTKey) .
				' FROM ' . implode(',', $fromTables) .
				( $where_sql	? ' WHERE '		. $where_sql	: '' ) .
				( $order_sql	? ' ORDER BY '	. $order_sql	: '' ) .
				( $limit_sql	? ' LIMIT '		. $limit_sql	: '' );

		if ($this->multiPrimary)
		{
			return db::rows($sql, $bind, $this->connectionName);
		}

		return db::cols($sql, $bind, $this->connectionName);
	}

	/**
	 * Insert row
	 *
	 * @param array $data
	 * @return integer
	 */
	public function doInsert($data, $multi = false)
	{

		if ($multi)
		{
			$columns = array();

			foreach ($data as $row)
			{
				if (!$columns)
				{
					$columns	=	array_keys($row);
				}
				
				$values		=	array_values($row);
				$values[]	=	time();

				foreach ($values as &$value)
				{
					$value	=	"'" . addslashes($value) . "'";
				}

				$sqlValues[]	=	"(" . implode(",", $values) . ")";
			}

			$columns[]	=	'created';
			$sqlColumns	=	implode(",", $columns);
			return db::exec('INSERT INTO ' . $this->tableName . ' (' . $sqlColumns . ') VALUES ' . implode(', ', $sqlValues), array(), $this->connectionName);
		}

		$data['created'] = time();

		$insert_data = array();

		foreach ($data as $column => $value)
		{
			$insert_data[] = "`{$column}` = :{$column}";
		}

		$sql	=	'INSERT INTO ' . $this->tableName . ' SET ' . implode(', ', $insert_data);


		db::exec($sql, $data, $this->connectionName);

		return db::lastId();
	}

	/**
	 * replace row
	 *
	 * @param array $data
	 * @return integer
	 */
	public function doReplace($data)
	{
		$data['created'] = time();

		$insert_data = array();

		foreach ($data as $column => $value)
		{
			$insert_data[] = "{$column} = :{$column}";
		}

		db::exec('REPLACE INTO ' . $this->tableName . ' SET ' . implode(', ', $insert_data), $data, $this->connectionName);

		return db::lastId();
	}

	/**
	 * update row by primary key
	 *
	 * @param integer $id
	 * @param array $data
	 * @return boolean
	 */
	public function doUpdate($primaryKey, $data)
	{
		foreach ($data as $column => $value)
		{
			$operand	=	false;

			if (strpos($column, ' ') !== false)
			{
				list($column, $operand)	=	explode(' ', $column);
				$operand				=	trim(strtolower($operand));
				$column	=	trim($column);
			}

			switch ($operand)
			{
				case '-=':
				case '+=':
					
					unset($data[$column . ' ' . $operand]);
					$data[$column]	=	(int)$value;
					$update_data[]	=	"{$column} = {$column} {$operand[0]} :{$column}";
					break;
				
				default:
					$update_data[] = "`{$column}` = :{$column}";
					break;
			}

		}

		$data = $this->doBindPrimaryKey($primaryKey, $data);
		

		return db::exec('UPDATE ' . $this->tableName . ' SET ' . implode(', ', $update_data) . " WHERE {$this->primaryBind}", $data, $this->connectionName);
	}

	/**
	 * delete row
	 *
	 * @param integer $primaryKey
	 */
	public function doDelete($primaryKey)
	{
		if (is_array($primaryKey))
		{
			foreach ($primaryKey as $primaryKeyId)
			{
				$this->delete($primaryKeyId);
			}

			return true;
		}

		return db::exec('DELETE FROM ' . $this->tableName . ' WHERE ' . "{$this->primaryBind}", $this->doBindPrimaryKey($primaryKey), $this->connectionName);
	}

	/**
	 * Bind primary key
	 *
	 * @param array $primaryKey
	 * @return array
	 */
	public function doBindPrimaryKey($primaryKey, $binds = array())
	{
		if (!$binds)
		{
			return array_combine($this->primaryKey, (array) $primaryKey);
		}

		return array_merge($binds, array_combine($this->primaryKey, (array) $primaryKey));
	}

	public function doSetKey($key)
	{
		$this->primaryKey = $key;
	}

}
