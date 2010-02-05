<?php
class storage
{
	const STORAGE	= 1;
	const WEB		= 2;

	public static function preparePath($file)
	{
		self::createDir(pathinfo($file, PATHINFO_DIRNAME));
	}
	
	protected static function store($input, $output)
	{
		self::preparePath($output);
		return copy($input, $output);
	}

	protected static function subpath($id)
	{
        return '/' . chunk_split(base_convert(abs((int) $id), 10, 36), 1, '/');
	}

	protected static function createDir($dir)
	{
        $parts	=	(array)explode('/', $dir);

        foreach ($parts as $part)
        {
            if (empty($part) && $part !== '0')
            {
                $basedir .= '/';
                continue;
            }

            $basedir .= $part . '/';

            if (!file_exists($basedir))
            {
				mkdir($basedir, 0775);
            }
        }

        return true;
    }

	protected static function getFilename($id, $size = false)
	{
		if (!$size)
		{
			return $id;
		}

		return $id . '_' . $size . '_' . self::getHash($id, $size);

	}

	public static function getHash($id, $size = false)
	{
		return substr(md5( $size . conf::i()->supersalt . $id), 0, 12);
	}
}
?>
