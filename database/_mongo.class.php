<?php
class _mongo
{
	static protected $instances = array();

	/**
	 *
	 * @param <string> $connection
	 * @return Mongo
	 */
	static function i($alias = 'master')
	{
		if (!self::$instances[$alias])
		{
			self::$instances[$alias] = new Mongo(conf::i()->mongo['servers'][$alias]['connection']);
		}

		return self::$instances[$alias];
	}

	static function db($alias = 'master')
	{
		return _mongo::i($alias)->selectDB(conf::i()->mongo['servers'][$alias]['database']);
	}

	static function primary($id)
	{
		return array('_id' => _mongo::id($id));
	}

	static function id($id)
	{
		return ($id instanceof MongoId) ? $id : new MongoId($id);
	}
}
