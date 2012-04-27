<?php
class storage
{
	const STORAGE	= 1;
	const WEB		= 2;

	static function preparePath($file)
	{
		self::createDir(pathinfo($file, PATHINFO_DIRNAME));
	}
	
	static function store($input, $output)
	{
		self::preparePath($output);
		return copy($input, $output);
	}

	static function subpath($id)
	{
		switch(conf::$conf['database']['engine'])
		{
			case	'mysql':
		        return '/' . chunk_split(base_convert(abs((int) $id), 10, 36), 1, '/');
				break;

			case	'mongo':
		        return '/' . chunk_split(substr(strrev((string) $id), 4), 2, '/');
				break;
		}

	}

	protected static function createDir($dir)
	{
		$basedir	=	'';
        $parts		=	(array)explode('/', $dir);

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

	static function getFilename($id, $size = false)
	{
		if (!$size)
		{
			return $id;
		}

		return $id . '_' . $size . '_' . self::getHash($id, $size);

	}

	public static function getHash($id, $size = false)
	{
		return substr(md5( $size . conf::$conf['supersalt'] . $id), 0, 12);
	}
}

