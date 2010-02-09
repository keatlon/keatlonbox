<?php
class deploymentPlan
{
	static private $document	= false;
	static private $default		= false;
	static $metadata			= false;
	static $plan				= false;
	static $environment			= false;
	static $release				= false;

	static public function load($name = 'plan')
	{
		self::$document = simplexml_load_file( self::getDir('${plan}', 'system') . '/' . $name . '.xml');

		foreach(self::$document->environment as $environmentXml)
		{
			$environment = false;
			$environment['default'] = (bool)$environmentXml['default'];
			$environment['hidden']	= (string)$environmentXml['hidden'];

			foreach($environmentXml->plan as $planXml)
			{
				$plan['default']	= (bool)$planXml['default'];
				$plan['name']		= (string)$planXml['name'];

				$environment['plans'][] = $plan;
			}

			self::$metadata['environments'][(string)$environmentXml['name']] = $environment;
		}

		self::$metadata['releases']			= self::getReleases();

	}

	static function prepare($environment, $plan, $release)
	{
		self::$plan			= $plan;
		self::$environment	= $environment;
		self::$release		= $release;

		self::setVar('release', $release);
		self::setVar('environment', $environment);

		deploymentQueue::init($environment, $plan, $release);

		$xmlTasks		= self::$document->xpath("/project/environment[@name='{$environment}']/plan[@name='{$plan}']/task");

		foreach($xmlTasks as $xmlTask)
		{
			$task = deploymentTaskFactory::create($xmlTask, $environment, $plan, $release);
			$task->prepare();
		}
	}

	static function getReleases()
	{
		$dh = opendir(self::getDir('${plan}', 'system'));

		while (($file = readdir($dh)) !== false)
		{
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
	 * Get default environment
	 */
	static function getDefaultEnvironment()
	{
		foreach (self::$metadata['environments'] as $name => $data)
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
	static function getDefaultPlan($environment)
	{
		foreach (self::$metadata['environments'][$environment]['plans'] as $plan)
		{
			if ($plan['default'])
			{
				return $plan['name'];
			}
		}
	}


	static function getHost($host, $environment = false)
	{
		if (!$environment)
		{
			$environment = self::$environment;
		}
		
		return conf::i()->deployment[$environment]['host'][$host];
	}

	static function setDir($alias, $directory, $environment = false)
	{
		if (!$environment)
		{
			$environment = self::$environment;
		}

		conf::i()->deployment[$environment]['dir'][$alias] = $directory;
	}

	static function match_constants($matches)
	{
		return constant($matches[1]);
	}

	static function match_var($matches)
	{
		return $$matches[1];
	}

	static function getDir($alias, $environment = false)
	{
		if (!$environment)
		{
			$environment = self::$environment;
		}

		$dir = $alias;

		if (conf::i()->deployment[$environment]['dir'])
		foreach(conf::i()->deployment[$environment]['dir'] as $dirName => $dirPath)
		{
			$dir = str_replace('${' . $dirName . '}', $dirPath, $dir);
		}

		if (conf::i()->deployment[$environment]['var'])
		foreach(conf::i()->deployment[$environment]['var'] as $key => $value)
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
		return self::getDir('release', self::$environment) . '/' . release;
	}


	static function setVar($key, $value, $environment = false)
	{
		if (!$environment)
		{
			$environment = self::$environment;
		}

		conf::i()->deployment[$environment]['var'][$key] = $value;
	}

	static function getVar($key, $environment = false)
	{
		if (!$environment)
		{
			$environment = self::$environment;
		}

		return conf::i()->deployment[$environment]['var'][$key];
	}

}
?>

