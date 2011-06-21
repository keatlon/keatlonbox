<?

abstract class dbPeer
{

	protected static $instances = array();

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
	public function doGetList($where = array(), $join = array(), $order = array(), $limit = false, $offset = false, &$total = false)
	{
		$bind			= array();
		$where_clause	= array();
		$join_clause	= array();
		$fromTables[]	= $this->tableName;

		if ($join)
			foreach ($join as $table => $conditions)
			{

				$joinType = '';
				$joinCondition = $condition;

				switch ($joinType)
				{
					case 'left':
						$join_clause[] = 'LEFT JOIN ' . $table . ' ON ' . $joinCondition;
						break;

					case 'right':
						$join_clause[] = 'RIGHT JOIN ' . $table . ' ON ' . $joinCondition;
						break;

					case 'cross':
						$join_clause[] = 'CROSS JOIN ' . $table . ' ON ' . $joinCondition;
						break;

					default:
						$fromTables[] = $table;
						foreach ($conditions as $conditionKey => $conditionValue)
						{
							if ($conditionValue[0] == ':')
							{
								$where[$conditionKey] = $conditionValue;
							}
							else
							{
								$where_clause[] = $conditionKey . ' = ' . $conditionValue;
							}
						}
				}
			}

		if (is_array($where))
			foreach ($where as $key => $value)
			{
				$bindKey = str_replace('.', '_', $key);

				switch ($key[0])
				{
					case '!':
						$key = substr($key, 1);
						$bindKey = str_replace('.', '_', $key);
						$where_clause[] = "{$key} <> :{$bindKey}";
						break;

					case '>':

						if ($key[1] == '=')
						{
							$key = substr($key, 2);
							$bindKey = str_replace('.', '_', $key);
							$where_clause[] = "{$key} >= :{$bindKey}";
						}
						else
						{
							$key = substr($key, 1);
							$bindKey = str_replace('.', '_', $key);
							$where_clause[] = "{$key} > :{$bindKey}";
						}

						break;

					case '<':
						$key = substr($key, 1);
						$bindKey = str_replace('.', '_', $key);
						$where_clause[] = "{$key} < :{$bindKey}";
						break;

					case '%':
						$key = substr($key, 1);
						$bindKey = str_replace('.', '_', $key);
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
			$limit = $offset . ', ' . $limit;
		}

		/**
		 * GET TOTAL ROWS QUERY
		 */
		$countSql = 'SELECT COUNT(' . $this->tableName . '.' . $this->primaryKey[0] . ') cnt ' . ' FROM ' . implode(',', $fromTables) .
			( $where_sql ? ' WHERE ' . $where_sql : '' );

		$countRow	= db::row($countSql, $bind, $this->connectionName);
		$total		= $countRow['cnt'];

		

		/**
		 * GET TOTAL ROWS QUERY
		 */
		$sql =	' SELECT ' . implode(',', $this->primaryTKey) .
				' FROM ' . implode(',', $fromTables) .
				( $where_sql	? ' WHERE '		. $where_sql	: '' ) .
				( $order_sql	? ' ORDER BY '	. $order_sql	: '' ) .
				( $limit		? ' LIMIT '		. $limit		: '' );

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
	public function doInsert($data)
	{
		$data['created'] = time();

		$insert_data = array();

		foreach ($data as $column => $value)
		{
			$insert_data[] = "{$column} = :{$column}";
		}

		db::exec('INSERT INTO ' . $this->tableName . ' SET ' . implode(', ', $insert_data), $data, $this->connectionName);

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
			$update_data[] = "{$column} = :{$column}";
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
