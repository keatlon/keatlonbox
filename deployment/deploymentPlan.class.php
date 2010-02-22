<?php
class deploymentPlan
{
	static private $document	= false;
	static private $default		= false;
	static $metadata			= false;
	static $space				= false;
	static $plan				= false;
	static $release				= false;

	static public function load($name = 'plan')
	{
		self::$document = simplexml_load_file( self::getDir('${plan}', 'system') . '/' . $name . '.xml');

		foreach(self::$document->space as $spaceXml)
		{
			$space = false;
			$space['default'] = (bool)$spaceXml['default'];
			$space['hidden']	= (string)$spaceXml['hidden'];

			foreach($spaceXml->plan as $planXml)
			{
				$plan['default']	= (bool)$planXml['default'];
				$plan['name']		= (string)$planXml['name'];

				$space['plans'][] = $plan;
			}

			self::$metadata['spaces'][(string)$spaceXml['name']] = $space;
		}

		self::$metadata['releases']			= self::getReleases();

	}

	static function prepare($space, $plan, $release)
	{
		self::$plan			= $plan;
		self::$space	= $space;
		self::$release		= $release;

		self::setVar('release', $release);
		self::setVar('space', $space);

		deploymentQueue::init($space, $plan, $release);

		$xmlTasks		= self::$document->xpath("/project/space[@name='{$space}']/plan[@name='{$plan}']/task");

		foreach($xmlTasks as $xmlTask)
		{
			$task = deploymentTaskFactory::create($xmlTask, $space, $plan, $release);
			$task->prepare();
		}
	}

	static function getReleases()
	{
		$dh = opendir(self::getDir('${plan}', 'system'));

		while (($file = readdir($dh)) !== false)
		{
			if ($file == 'conf')
			{
				continue;
			}
			
			if (is_dir(self::getDir('${plan}', 'system') . '/' . $file) && $file[0] != '.')
			{
				$info = pathinfo($file);
				$releases[] = $info['basename'];
			}
        }

        closedir($dh);

		natsort($releases);

		return $releases;
	}

	static function getDefaultRelease()
	{
		return self::$metadata['releases'][count(self::$metadata['releases']) - 1];
	}

	/*
	 * Get default space
	 */
	static function getDefaultspace()
	{
		foreach (self::$metadata['spaces'] as $name => $data)
		{
			if ($data['default'])
			{
				return $name;
			}
		}
	}

	/*
	 * Get default plan
	 */
	static function getDefaultPlan($space)
	{
		foreach (self::$metadata['spaces'][$space]['plans'] as $plan)
		{
			if ($plan['default'])
			{
				return $plan['name'];
			}
		}
	}


	static function getHost($host, $space = false)
	{
		if (!$space)
		{
			$space = self::$space;
		}
		
		return conf::i()->deployment[$space]['host'][$host];
	}

	static function setDir($alias, $directory, $space = false)
	{
		if (!$space)
		{
			$space = self::$space;
		}

		conf::i()->deployment[$space]['dir'][$alias] = $directory;
	}

	static function match_constants($matches)
	{
		return constant($matches[1]);
	}

	static function match_vars($matches)
	{
		return $$matches[1];
	}

	static function getDir($alias, $space = false)
	{
		if (!$space)
		{
			$space = self::$space;
		}

		$dir = $alias;

		if (conf::i()->deployment[$space]['dir'])
		foreach(conf::i()->deployment[$space]['dir'] as $dirName => $dirPath)
		{
			$dir = str_replace('${' . $dirName . '}', $dirPath, $dir);
		}

		if (conf::i()->deployment[$space]['var'])
		foreach(conf::i()->deployment[$space]['var'] as $key => $value)
		{
			$dir = str_replace('${' . $key . '}', $value, $dir);
		}
		
		$dir = preg_replace_callback('|\${const:(\w*)}|', array(self, 'match_constants'), $dir);
		$dir = preg_replace_callback('|\${var:(\w*)}|', array(self, 'match_vars'), $dir);

		if ($dir)
		{
			return $dir;
		}

		return $dir;
	}

	static function getReleaseDir($release)
	{
		return self::getDir('release', self::$space) . '/' . release;
	}


	static function setVar($key, $value, $space = false)
	{
		if (!$space)
		{
			$space = self::$space;
		}

		conf::i()->deployment[$space]['var'][$key] = $value;
	}

	static function getVar($key, $space = false)
	{
		if (!$space)
		{
			$space = self::$space;
		}

		return conf::i()->deployment[$space]['var'][$key];
	}

}
?>

