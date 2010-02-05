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
		self::$document = simplexml_load_file( self::getDir('deployment') . '/' . $name . '.xml');

		foreach(self::$document->environment as $environmentXml)
		{
			$environment = false;
			$environment['default'] = (bool)$environmentXml['default'];

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
		$dh = opendir(self::getDir('deployment'));

		while (($file = readdir($dh)) !== false)
		{
			if (is_dir(self::getDir('deployment') . '/' . $file) && $file[0] != '.')
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

	static function setDir($alias, $directory)
	{
		conf::i()->deployment['dir'][$alias] = $directory;
	}

	static function getHost($host)
	{
		return conf::i()->deployment['host'][$host];
	}

	static function getDir($alias)
	{
		$dir = conf::i()->deployment['dir'][$alias];

		if ($dir)
		{
			return $dir;
		}

		switch($alias)
		{
			case 'current_release_sources_dir':
				return conf::i()->deployment['dir']['release'] . '/' . self::$release;
				break;
		}

		return false;
	}

	static function getReleaseDir($release)
	{
		return self::getDir('release') . '/' . release;
	}

}
?>

