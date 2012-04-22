<?php

function __autoload($className)
{
	$filename = router::get($className);
	if (!$filename)
	{
	    die('class ' . $className . ' not found');
	}
	
	require $filename;
}

function h($value)
{
	return htmlspecialchars($value, ENT_QUOTES);
}

function d($value, $return = false)
{
	if (is_null($value))
	{
		$v = "NULL";
	}
	elseif (is_bool($value))
	{
		if ($value)
		{
			$v = 'TRUE';
		}
		else
		{
			$v = 'FALSE';
		}
	}
	elseif (is_numeric($value))
	{
		$v = (string)$value;
	}
	else
	{
		$v = print_r($value, true);
	}

    if ($return) 
	{
		return print_r($v, true);
	}
    else
	{
		echo "<pre>" . $v . "</pre>";
	}
}

function dd($value)
{
	d($value);
	die();
}

function __($phrase)
{
    return i18n::get($phrase);
}

function scan($dir, $regexp)
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
		if ($file == '.' || $file == '..' )
		{
			continue;
		}

		if (is_dir($dir . '/' .$file))
		{
			$files = array_merge($files, (array)scan($dir . '/' . $file, $regexp));
		}
		else
		{
			if (preg_match($regexp, $file, $matches))
			{
				$files[] = $dir . '/' . $file;
			}
		}

	}

	closedir($handle);
	return $files;
}
