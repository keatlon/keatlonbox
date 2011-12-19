<?php

class ReinforcedMongoCollection extends MongoCollection
{

	protected function dottoa($field, $value = false)
	{
		$parts  =   explode('.', $field);
		$last   =   array_pop($parts);
		$json   =   '{"' . implode($parts, '" : {"') . '" : {"' . $last . '" : ' . json_encode($value) . str_repeat("}", count($parts)) . '}';

		return json_decode($json, true);
	}

	protected function criteria($criteria)
	{
		if (!is_array($criteria))
		{
			$criteria   =   _mongo::primary($criteria);
		}

		return $criteria;
	}

	/**
	 * Get document id by given criteria
	 *
	 * @param $pkey
	 */
	function id($criteria)
	{
		$row	=	$this->findOne($criteria);
		return	(bool)$row ? $row['_id'] : false;
	}

	function get($pkey)
	{
		return $this->findOne(_mongo::primary($pkey));
	}


	function set($pkey, $data)
	{
		$this->update(_mongo::primary($pkey), array('$set' => $data));
	}

	function _unset($pkey, $field)
	{
		$this->update(_mongo::primary($pkey), array('$unset' => $field));
	}

	function push($pkey, $field, $data)
	{
		$this->update(_mongo::primary($pkey), array('$push' => array($field => $data)));
	}

	function inc($criteria, $fields, $inc = 1)
	{
		$criteria   =   $this->criteria($criteria);

		if (is_array($fields))
		{
			return $this->update($criteria, array('$inc' => $fields));
		}

		return $this->update($criteria, array('$inc' => array($fields => $inc)));
	}

}