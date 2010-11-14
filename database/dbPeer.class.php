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

	public function doSetKey( $key )
	{
		$this->primaryKey = $key;
	}

	/**
	 * get full row from table
	 *
	 * @param integer $primaryKey
	 * @return array
	 */
	public function doGetItem( $primaryKey )
	{
		if (is_array($primaryKey))
		{
			$primaryKey = $primaryKey[0];
		}

		$mcKey  = mc::buildKey(array(get_class($this), __FUNCTION__), array($primaryKey));
		$row    = mc::get($mcKey);
		if ($row === NULL)
		{
			$bind['id'] = $primaryKey;
			$row = db::row( 'SELECT * FROM ' . $this->tableName . " WHERE {$this->primaryKey} = :id LIMIT 1", $bind, $this->connectionName );
			mc::set($mcKey, $row);
		}

		return $row;
	}


	public function doGetItems( $primaryKeys )
	{
		if (!is_array($primaryKeys) || !$primaryKeys)
		{
			return array();
		}

		foreach($primaryKeys as $primaryKey)
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
	public function doGetList( $where = array(), $join = array(), $order = array(), $limit = false, $offset = false, &$total = false )
	{
		$bind           = array();
		$where_clause   = array();
		$join_clause	= array();
		$fromTables[]	= $this->tableName;

		if ($join) foreach ( $join as $table => $conditions )
		{

			$joinType = '';
			$joinCondition = $condition;

			switch($joinType)
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
					foreach($conditions as $conditionKey => $conditionValue)
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

		if (is_array($where)) foreach ( $where as $key => $value )
		{
			$bindKey = str_replace('.', '_', $key);

			switch($key[0])
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

			if ( !$order )
			{
				$order = array( $this->primaryKey . ' DESC ');
			}

			$where_sql = implode(' AND ', $where_clause);
			$order_sql = implode(', ', $order);

			if ($limit && $offset)
			{
				$limit = $offset . ', ' . $limit;
			}

			$countSql = 'SELECT COUNT(' . $this->tableName . '.' . $this->primaryKey . ') cnt ' .  ' FROM ' . implode(',', $fromTables) .
			( $where_sql ? ' WHERE ' . $where_sql : '' );

			$countRow	=	db::row( $countSql, $bind, $this->connectionName );
			$total		=	$countRow['cnt'];

			$sql = 'SELECT ' . $this->tableName . '.' . $this->primaryKey . ' ' . $this->primaryKey . '
		FROM ' . implode(',', $fromTables) .
			( $where_sql ? ' WHERE ' . $where_sql : '' ) .
			( $order_sql ? ' ORDER BY ' . $order_sql : '' ) .
			( $limit ? ' LIMIT ' . $limit : '' );

			return db::cols( $sql, $bind, $this->connectionName );
		}

		/**
	 * insert row
	 *
	 * @param array $data
	 * @return integer
	 */
		public function doInsert( $data )
		{
			$data['created'] = time();

			$insert_data = array();

			foreach ( $data as $column => $value )
			{
				$insert_data[] = "{$column} = :{$column}";
			}

			db::exec('INSERT INTO ' . $this->tableName . ' SET ' . implode(', ', $insert_data), $data, $this->connectionName);

			return db::lastId();
		}

	/**
	 * insert row
	 *
	 * @param array $data
	 * @return integer
	 */
		public function doReplace( $data )
		{
			$data['created'] = time();

			$insert_data = array();

			foreach ( $data as $column => $value )
			{
				$insert_data[] = "{$column} = :{$column}";
			}

			mc::delete( mc::buildKey(array(get_class($this), 'getItem'), array($data[$this->primaryKey])) );

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
		public function doUpdate($id,  $data)
		{
			foreach ( $data as $column => $value )
			{
				$update_data[] = "{$column} = :{$column}";
			}

			$data['id'] = $id;
			mc::delete( mc::buildKey(array(get_class($this), 'getItem'), array($id)) );
			return db::exec('UPDATE ' . $this->tableName . ' SET ' . implode(', ', $update_data) . ' WHERE ' . "{$this->primaryKey} = :id", $data, $this->connectionName);
		}

	/**
	 * delete row
	 *
	 * @param integer $primaryKey
	 */
		public function doDelete( $primaryKey )
		{
			if (is_array($primaryKey))
			{
				foreach($primaryKey as $primaryKeyId)
				{
					$this->delete($primaryKeyId);
				}

				return true;
			}

			mc::delete( mc::buildKey(array(get_class($this), 'getItem'), array($primaryKey)) );

			return db::exec( 'DELETE FROM ' . $this->tableName . ' WHERE ' . "{$this->primaryKey} = :id", array('id' => $primaryKey), $this->connectionName );
		}

	}