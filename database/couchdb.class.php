<?php

class couchdb
{
	static function url()
	{
		return conf::$conf['couchdb']['url'];
	}

	static function delete($type, $id)
	{
		return self::response(curl::delete(self::url() . "/$type/$id"));
	}

	static function update($type, $id, $data)
	{
		return self::response(curl::put(self::url() . "/$type/$id", json_encode($data), array('Content-Type:application/json')));
	}

	static function add($type, $data)
	{
		$response	=	curl::post(self::url() . "/$type", json_encode($data), array('Content-Type: application/json'), $info);
		return self::response($response, $info);
	}

	static function get($type, $id)
	{
		return self::response(curl::get(self::url() . "/$type/$id"));
	}

	static protected function response($response, $info = false)
	{
		if (!$response)
		{
			dd($info);
		}

		$response	=	json_decode($response, true);

		if ($response['error'])
		{
			return false;
		}

		return $response;
	}

}