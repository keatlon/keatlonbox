<?php

require conf::$conf['rootdir'] . '/core/library/elasticsearch/ElasticSearchClient.php';

class elastic
{

	protected static $transport =	false;
	protected static $instance 	=	false;

	/**
	 * @static
	 * @return ElasticSearchClient
	 */
	static function i()
	{
		if (!self::$instance)
		{
			self::$transport 	=	new ElasticSearchTransportHTTP("localhost", 9200);
			self::$instance		=	new ElasticSearchClient(self::$transport, "zapomni", "word");
		}

		return self::$instance;
	}

	static function update($id, $data)
	{
		return self::i()->index($data, $id);
	}

	static function get($id)
	{
		return self::i()->get($id);
	}

	static function search($query)
	{
		return self::i()->search($query);
	}
}