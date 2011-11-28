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

	function get($pkey)
	{
		return $this->findOne(_mongo::primary($pkey));
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

	function clear($criteria, $field, $value = false)
	{
		$criteria       =   $this->criteria($criteria);
		$criteria[str_replace('.$', '', $field)]    =   $value;

		dd($this->findOne($criteria));

		$data   =   array('$unset' => array('skills.items.$.privacy' => "public" ));
//		$data   =   array('$unset' => array('skills.items.$.privacy' => "public" ));

		return $this->update($criteria, $data);
	}

	function pop($criteria, $field)
	{

	}

	function push($criteria, $field, $data)
	{

	}

	function pushAll($where, $what)
	{

	}

	function pull($where, $what)
	{

	}

	function pullAll($where, $what)
	{

	}

	function set($pkey, $where, $what)
	{

	}


	function bit($pkey, $where, $what)
	{

	}

	function addToSet($pkey, $where, $what)
	{

	}

}