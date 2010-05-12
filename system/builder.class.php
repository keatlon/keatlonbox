<?php
class builder
{
    static function arrayToCode($items)
    {
        $result = '';

        if (!$items)
        {
            return $result;
        }

        foreach($items as $name => $path)
        {
            $result .= "\t'{$name}'\t\t\t\t=>'{$path}',\n";
        }

        return $result;
    }

    static public function scanClasses($path)
    {
        $files = builder::_readdir($path, '|(.*)\.class\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9]+)\.class\.php#', $path, $matches))
            {
                $result[$matches[1]] = $path;
            }
        }

        return (array)$result;
    }

    static public function scanTasks($path)
    {
        $files = builder::_readdir($path, '|(.*)\.task\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)\.task\.php#', $path, $matches))
            {
                $result[$matches[2] . ucfirst($matches[1]) . 'Controller'] = $path;
            }
        }

        return $result;
    }

    static public function scanActions($path)
    {
        $files = builder::_readdir($path, '|(.*)\.action\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9]+)/action/([a-zA-Z0-9]+)\.action\.php#', $path, $matches))
            {
                $result[$matches[2] . ucfirst($matches[1]) . 'Controller'] = $path;
            }
        }

        return $result;
    }

    static public function scanPHP($path)
    {
        $files = builder::_readdir($path, '|(.*)\.php$|');

        foreach($files as $path)
        {
            if (preg_match('#/([a-zA-Z0-9_\.]*)\.php#', $path, $matches))
            {
                $result[$matches[1]] = $path;
            }
        }

        return $result;
    }

    static public function _readdir($dir, $pattern)
    {
        $files = array();

		if (!file_exists($dir))
		{
			return $files;
		}

        $handle = opendir($dir);

        if (!$handle)
        {
            return false;
        }

        while (false !== ($file = readdir($handle)))
        {
            if ($file == '.' || $file == '..' || $file == '.svn' )
            {
                continue;
            }

            if (is_dir($dir . '/' .$file))
            {
                $files = array_merge($files, (array)builder::_readdir($dir . '/' . $file, $pattern));
            }
            else
            {
                if (preg_match($pattern, $file, $matches))
                {
                    $files[] = $dir . '/' . $file;
                }
            }

        }

        closedir($handle);
        return $files;
    }

}


?>
