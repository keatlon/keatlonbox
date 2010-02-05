<?php
class imageStorage extends storage
{
	static function save($tmpFile, $originalName = false)
	{
		$data['name'] = $originalName;
		$data['crc32'] = sprintf('%u', crc32(file_get_contents($tmpFile)));

		$id	= imagePeer::insert($data);

		if (!storage::store($tmpFile, imageStorage::storagePath($id)))
		{
			imagePeer::delete($id);
			return false;
		}
		
		return $id;
	}

	static function storagePath($id)
	{
		return conf::i()->image['storage'] . self::subpath($id) . self::getFilename($id) . '.jpg';
	}

	static function cachePath($id, $size, $full = true)
	{
		if ($full)
		{
			return conf::i()->image['cache'] . self::subpath($id) . self::getFilename($id, $size) . '.jpg';
		}
		
		return self::subpath($id) . self::getFilename($id, $size) . '.jpg';
	}

	static function webPath($id, $size, $fullPath = true, $suffix = false)
	{
		if ($suffix)
		{
			$suffix = '_' . str_replace(' ', '_' , $suffix);
		}

	    if ($fullPath)
	    {
			return conf::i()->domains['image'] . self::subpath($id) . self::getFilename($id, $size) . $suffix . '.jpg';
	    }
	    
	    return self::subpath($id) . self::getFilename($id, $size) . '.jpg';
	}

	static function crop($id, $sourceType, $destinationType, $width, $height, $offsetX, $offsetY)
	{
        if ($sourceType)
        {
            $sourceFile = self::cachePath($id, $sourceType);
        }
        else
        {
            $sourceFile = self::storagePath($id);
        }

		$originalFile = self::storagePath($id);

		$sourceSize		= getimagesize($sourceFile);
		$sourceWidth	= $sourceSize[0];
		$sourceHeight	= $sourceSize[1];

		$originalSize	= getimagesize($originalFile);
		$originalWidth	= $originalSize[0];
		$originalHeight	= $originalSize[1];

        $destinationFile = self::cachePath($id, $destinationType);

		$original['top']	= floor($offsetY * $originalHeight / $sourceHeight);
		$original['left']	= floor($offsetX * $originalWidth / $sourceWidth);
		$original['height']	= floor($height * $originalHeight / $sourceHeight);
		$original['width']	= floor($width * $originalWidth / $sourceWidth);

        storage::preparePath($destinationFile);

        $cmd = conf::i()->image['imagick'] . ' ' . $originalFile . ' -crop '. $original['width'] . 'x' . $original['height'] . '+' . $original['left'] . '+' . $original['top'] . ' -quality 90 ' . $destinationFile;

        if (conf::i()->image['escapecmd'])
        {
            $cmd = escapeshellcmd($cmd);
        }

        exec($cmd);

		return $percent;
	}

	static function convert($id, $sourceType, $destinationType)
	{
        if ($sourceType)
        {
            $sourceFile = self::cachePath($id, $sourceType);
        }
        else
        {
            $sourceFile = self::storagePath($id);
        }
        
        $options = conf::i()->image['sizes'][$destinationType];

        $destinationFile = self::cachePath($id, $destinationType);

        storage::preparePath($destinationFile);

        $cmd = conf::i()->image['imagick'] . ' ' . $sourceFile . ' ' .$options . ' ' .$destinationFile;

        if (conf::i()->image['escapecmd'])
        {
            $cmd = escapeshellcmd($cmd);
        }

        return exec($cmd);
    }

    static function getSquareCoords($id, $sourceType = false, $verticalAxis = 25, $horizontalAxis = 50)
    {
        if ($sourceType)
        {
            $sourceFile = self::cachePath($id, $sourceType);
        }
        else
        {
            $sourceFile = self::storagePath($id);
        }

        $size       = self::getSize($sourceFile);

        if ($size['width'] > $size['height'])
        {
            $side = $size['height'];
            $offsetTop  = 0;
            $offsetLeft = ($size['width'] - $side) * $horizontalAxis / 100;
        }
        elseif ($size['width'] <= $size['height'])
        {
            $side = $size['width'];
            $offsetTop  = ($size['height'] - $side) * $verticalAxis / 100;
            $offsetLeft = 0;
        }

        return array(
            't' => $offsetTop,
            'l' => $offsetLeft,
            'w' => $side,
            'h' => $side
        );
    }

    static function getSize($sourceFile)
    {
        $size = getimagesize($sourceFile);
        return array('width' => $size[0], 'height' => $size[1]);
    }

    
}
?>
