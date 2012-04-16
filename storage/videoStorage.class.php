<?php
class videoStorage extends storage
{

	static function save($type, $filename)
	{
		$id	= videoPeer::insert(array('user_id' => auth::id(), 'type' => $type));

		if (!storage::store($filename, videoStorage::storagePath($id)))
		{
			videoPeer::delete($id);
			return false;
		}

		return $id;
	}

	static function storagePath($id)
	{
		return conf::i()->video['storage'] . self::subpath($id) . self::getFilename($id);
	}

	static function cachePath($id, $size = 'normal')
	{
		return conf::i()->video['cache'] . self::subpath($id) . self::getFilename($id, $size) . '.flv';
	}

	static function webPath($id, $size = 'normal')
	{
		if (!$id)
		{
			return false;
		}

		return conf::i()->domains['video'] . self::subpath($id) . self::getFilename($id, $size) . '.flv';
	}
}

