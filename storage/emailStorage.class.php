<?
abstract class emailStorage
{

	static function insert($data)
	{
		switch(conf::$conf['database']['engine'])
		{
			case 'mysql':
				return emailStorageMysql::insert($data);

			case 'mongo':
				return emailStorageMongo::insert($data);
		}
	}

	static function get($id)
	{
		switch(conf::$conf['database']['engine'])
		{
			case 'mysql':
				return emailStorageMysql::get($id);

			case 'mongo':
				return emailStorageMongo::get($id);
		}
	}

	static function delete($id)
	{
		switch(conf::$conf['database']['engine'])
		{
			case 'mysql':
				return emailStorageMysql::delete($id);

			case 'mongo':
				return emailStorageMongo::delete($id);
		}
	}

	static function getNew($limit)
	{
		switch(conf::$conf['database']['engine'])
		{
			case 'mysql':
				return emailStorageMysql::getNew($limit);

			case 'mongo':
				return emailStorageMongo::getNew($limit);
		}
	}
}
