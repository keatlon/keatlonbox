<?

abstract class dbPeer
{

	protected static $instances =   array();
    protected static $lastId    =   false;

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
	public function doRow($pk, $alias = false)
	{
		if (!$pk)
		{
			return false;
		}

		if (is_array($pk))
		{
			$pk = $pk[0];
		}

		$row = db::row('SELECT * FROM `' . $this->tableName . "` WHERE {$this->primaryBind} LIMIT 1", $this->doBindPrimaryKey($pk), $alias ? $alias : $this->alias);

		if ($row['meta'])
		{
			$row['meta']	=	json_decode($row['meta'], true);
		}

		return $row;
	}

	public function doRows($primaryKeys, $alias = false)
	{
		if (!is_array($primaryKeys) || !$primaryKeys)
		{
			return array();
		}

		foreach ($primaryKeys as $primaryKey)
		{
			$result[] = $this->doRow($primaryKey, $alias);
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
	public function doCols(	$where 		=	array(),
						   	$order 		=	array(),
						   	$limit 		=	false,
						   	$offset 	=	false,

						   	&$total 	=	null,
						   	&$more 		=	null,

						   	$fields 	=	false,
							$selectSql	=	false,
							$fromSql	=	false,
							$alias	=	false
	)
	{
		$bind			= array();
		$where_clause	= array();
		$fromTables[]	= '`' . $this->tableName . '`';

		if (is_array($where))
			foreach ($where as $key => $value)
			{
				if (is_numeric($key))
				{
					$where_clause[] = $value;
					continue;
				}

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
						$where_clause[] = self::escape($key) . " IN (:{$bindKey})";
						break;

					case '!=':
						$where_clause[] = self::escape($key) . " <> :{$bindKey}";
						break;

					case '>':
						$where_clause[] = self::escape($key) . " > :{$bindKey}";
						break;

					case '>=':
						$where_clause[] = self::escape($key) . " >= :{$bindKey}";
						break;

					case '<':
						$where_clause[] = self::escape($key) . " < :{$bindKey}";
						break;

					case '<=':
						$where_clause[] = self::escape($key) . " <= :{$bindKey}";
						break;

					case '%':
						$where_clause[] = self::escape($key) . " LIKE :{$bindKey}";
						$value = '%' . $value . '%';
						break;

					default:
						$where_clause[] = self::escape($key) . " = :{$bindKey}";
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
		}
		else if ($limit)
		{
			$limit_sql = $limit;
		}

		if (!$fields)
		{
			$fields	=	$this->primaryTKey;
		}
		else
		{
			if (!is_array($fields))
			{
				$fields = array($fields);
			}
		}

		if (!$selectSql)
		{
			$selectSql		=	' SELECT ' . implode(',', $fields);
		}

		if (!$fromSql)
		{
			$fromSql	=	' FROM ' . implode(',', $fromTables);
		}

		/**
		 * GET TOTAL ROWS QUERY
		 */
		if (false && $total !== null)
		{
			$countSql = ' SELECT count(' . $this->primaryCKey . ') cnt ' . $fromSql .
				( $where_sql ? ' WHERE ' . $where_sql : '' );

			$countRow	= 	db::row($countSql, $bind, $alias ? $alias : $this->alias);
			$total		= 	$countRow['cnt'];
			$more		=	$total > ((int)$offset + (int)$limit);
		}

		/**
		 * GET ROWS QUERY
		 */
		$sql =	$selectSql .
				$fromSql .
				( $where_sql	? ' WHERE '		. $where_sql	: '' ) .
				( $order_sql	? ' ORDER BY '	. $order_sql	: '' ) .
				( $limit_sql	? ' LIMIT '		. $limit_sql	: '' );


		if (count($fields) > 1)
		{
			return db::rows($sql, $bind, $alias ? $alias : $this->alias);
		}

		return db::cols($sql, $bind, $alias ? $alias : $this->alias);
	}

	/**
	 * Insert row
	 *
	 * @param array $data
	 * @return integer
	 */
	public function doInsert($data, $multi = false, $ignore = false, $alias = false)
	{
        $alias          =   $alias ? $alias : $this->alias;
		$ignore		    =	$ignore ? 'IGNORE' : '';
        $addCreated     =   false;

		if ($multi)
		{
			$columns = array();

            if (isset($data[0]['created']))
            {
                $addCreated = true;
            }

			foreach ($data as $row)
			{
				if (!$columns)
				{
					$columns	=	array_keys($row);
				}
				
				$values		=	array_values($row);

                if ($addCreated)
                {
                    $values[]	=	time();
                }

				foreach ($values as &$value)
				{
					$value	=	"'" . addslashes($value) . "'";
				}

				$sqlValues[]	=	"(" . implode(",", $values) . ")";
			}

            if ($addCreated)
            {
			    $columns[]	=	'created';
            }

			$sqlColumns	=	implode(",", $columns);

			$statement      =   db::exec('INSERT ' . $ignore . ' INTO ' . self::escape($this->tableName) . ' (' . $sqlColumns . ') VALUES ' . implode(', ', $sqlValues), array(), $alias ? $alias : $this->alias);

            return $statement->rowCount();
		}

		$data['created'] = time();

		$insert_data = array();

		foreach ($data as $column => $value)
		{
			$insert_data[] = self::escape($column) . " = :{$column}";
		}

		$sql	=	'INSERT ' . $ignore . ' INTO ' . self::escape($this->tableName) . ' SET ' . implode(', ', $insert_data);

		$statement      =   db::exec($sql, $data, $alias);
        db::$affected   =   $statement->rowCount();

		return db::lastId($alias);
	}

	/**
	 * replace row
	 *
	 * @param array $data
	 * @return integer
	 */
	public function doReplace($data, $alias = false)
	{
		$data['created'] = time();

		$insert_data = array();

		foreach ($data as $column => $value)
		{
			$insert_data[] = self::escape($column) . " = :{$column}";
		}

		db::exec('REPLACE INTO ' . self::escape($this->tableName) . ' SET ' . implode(', ', $insert_data), $data, $alias ? $alias : $this->alias);

		return db::lastId();
	}

	/**
	 * update row by primary key
	 *
	 * @param integer $id
	 * @param array $data
	 * @return boolean
	 */
	public function doUpdate($primaryKey, $data, $alias = false)
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
					$update_data[] = self::escape($column) . " = :{$column}";
					break;
			}

		}

		$data = $this->doBindPrimaryKey($primaryKey, $data);
		
		return db::exec('UPDATE ' . self::escape($this->tableName) . ' SET ' . implode(', ', $update_data) . " WHERE {$this->primaryBind}", $data, $alias ? $alias : $this->alias);
	}

	/**
	 * delete row
	 *
	 * @param integer $primaryKey
	 */
	public function doDelete($primaryKey, $alias = false)
	{
		if (is_array($primaryKey))
		{
			foreach ($primaryKey as $primaryKeyId)
			{
				$this->delete($primaryKeyId, $alias);
			}

			return true;
		}

		return db::exec('DELETE FROM ' . self::escape($this->tableName) . ' WHERE ' . "{$this->primaryBind}", $this->doBindPrimaryKey($primaryKey), $alias ? $alias : $this->alias);
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

	static function escape($key)
	{
		$parts = explode('.', $key);

		foreach($parts as &$part)
		{
			if ($part[0] == '`')
			{
				continue;
			}

			$part = '`' . $part . '`';
		}

		return implode('.', $parts);
	}
}
