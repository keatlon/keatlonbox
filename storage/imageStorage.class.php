<?php
class imageStorage extends storage
{
	static function save($tmpFile)
	{
		$data	=	array();

		switch(conf::$conf['database']['engine'])
		{
			case	'mysql':
				$id	= imagePeer::insert($data);
				break;

			case	'mongo':
				images::i()->insert($data);
				$id	=	(string)$data['_id'];
				break;
		}


		if (!storage::store($tmpFile, imageStorage::storagePath($id)))
		{
			switch(conf::$conf['database']['engine'])
			{
				case	'mysql':
					imagePeer::delete($id);
					break;

				case	'mongo':
					images::i()->remove(_mongo::primary($id));
					break;
			}

			return false;
		}
		
		return $id;
	}

	static function storagePath($id)
	{
		return conf::$conf['image']['storage'] . self::subpath($id) . self::getFilename($id) . '.jpg';
	}

	static function cachePath($id, $size, $full = true)
	{
		if ($full)
		{
			return conf::$conf['image']['cache'] . self::subpath($id) . self::getFilename($id, $size) . '.jpg';
		}
		
		return self::subpath($id) . self::getFilename($id, $size) . '.jpg';
	}

	static function webPath($id, $size, $fullPath = true, $suffix = false, $type = 'all')
	{

		if ($suffix)
		{
			$suffix = '_' . str_replace(' ', '_' , $suffix);
		}

		if (!$id)
		{
			return conf::$conf['image']['default'][$type][$size];
		}

		$url	=	self::subpath($id) . self::getFilename($id, $size) . $suffix . '.jpg';

	    if ($fullPath)
	    {
			return conf::$conf['domains']['image'] . $url;
	    }
	    
	    return $url;
	}

	static function crop($id, $sourceType, $destinationType, $width, $height, $offsetX, $offsetY, $useOriginal = true)
	{
        if ($sourceType)
        {
            $sourceFile = self::cachePath($id, $sourceType);
        }
        else
        {
            $sourceFile = self::storagePath($id);
        }

		$sourceSize			=	getimagesize($sourceFile);
		$sourceWidth		=	$sourceSize[0];
		$sourceHeight		=	$sourceSize[1];

		$destinationFile	= self::cachePath($id, $destinationType);

		if ($useOriginal)
		{
			$originalFile 	= 	self::storagePath($id);
			$sourceFile		=	$originalFile;
			$originalSize	= 	getimagesize($originalFile);
			$originalWidth	= 	$originalSize[0];
			$originalHeight	= 	$originalSize[1];

			$rectangle['left']		=	floor($offsetX * $originalWidth / $sourceWidth);
			$rectangle['top']		=	floor($offsetY * $originalHeight / $sourceHeight);
			$rectangle['width']		=	floor($width * $originalWidth / $sourceWidth);
			$rectangle['height']	=	floor($height * $originalHeight / $sourceHeight);
		}
		else
		{
			$rectangle['left']		= 	$offsetX;
			$rectangle['top']		= 	$offsetY;
			$rectangle['width']		= 	$width;
			$rectangle['height']	= 	$height;
		}

		$resizeOptions = conf::$conf['image']['sizes'][$destinationType];

        storage::preparePath($destinationFile);

        $cmd = conf::$conf['image']['imagick'] . ' ' . $sourceFile . ' -crop '. $rectangle['width'] . 'x' . $rectangle['height'] . '+' . $rectangle['left'] . '+' . $rectangle['top'] . ' ' . $resizeOptions . ' ' . $destinationFile;

        if (conf::$conf['image']['escapecmd'])
        {
            $cmd = escapeshellcmd($cmd);
        }

        return exec($cmd);
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
        
        $options = conf::$conf['image']['sizes'][$destinationType];

        $destinationFile = self::cachePath($id, $destinationType);

        storage::preparePath($destinationFile);

        $cmd = conf::$conf['image']['imagick'] . ' ' . $sourceFile . ' ' .$options . ' ' .$destinationFile;

        if (conf::$conf['image']['escapecmd'])
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

