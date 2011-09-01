<?php
class imageHelper
{
	static $imagePeer = false;

	static function link($imageId, $size = false, $fullPath = true, $suffix = false)
	{
		return imageStorage::webPath($imageId, $size, $fullPath, $suffix);
	}

	static function profile($userId, $userData = false, $size = false, $fullPath = true, $suffix = false)
	{
		if (!$userData)
		{
			$userData	=	userDataPeer::getItem($userId);
		}
		
		$imageId	=	$userData['image_id'];

		if (!$imageId)
		{
			if ($fullPath)
			{
				$prefix	=	conf::i()->domains['image'];
			}

			return $prefix . '/images/default/user-' . $size . '.png';
		}

		return imageStorage::webPath($imageId, $size, $fullPath, $suffix);
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
