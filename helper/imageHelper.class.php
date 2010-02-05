<?php
class imageHelper
{
	static $imagePeer = false;

	static function link($imageId, $size = false, $fullPath = true, $suffix = false)
	{
		return imageStorage::webPath($imageId, $size, $fullPath, $suffix);
	}

	static function profile($userId, $size = false, $extra = array())
	{
		$image = userDataPeer::getItem($userId);
		$imageId = $image['image_id'];

		if (!self::$imagePeer)
		{
			self::$imagePeer = new imagePeer;
		}


		if (!$imageId)
		{
			$imageId = 0;
		}

		$imagePath = imageStorage::webPath($imageId, $size);

		$attrs = '';
		foreach($extra as $key => $value)
		{
			$attrs .= ' ' . $key . '="'.$value.'"';
		}

		return '<img src="' . $imagePath . '" '.$attrs.' />';
	}


	static function photo($imageId, $size = false, $extra = array(), $rand = false, $suffix = false)
	{
		if (!$imageId)
		{
			$imageId = 0;
		}

		$imagePath = imageStorage::webPath($imageId, $size, true, $suffix);

		$attrs = '';
		if ($extra) foreach($extra as $key => $value)
		{
			$attrs .= ' ' . $key . '="'.$value.'"';
		}

		if ($rand)
		{
			$imagePath .= '?' . rand(1, 1000000);
		}
		
		return '<img src="' . $imagePath . '" '.$attrs.' />';
	}

}
?>
