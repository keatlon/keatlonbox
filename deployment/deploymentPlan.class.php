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
		self::$document = simplexml_load_file( self::parse('${system::plan}') . '/' . $name . '.xml');

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
		self::$plan		= $plan;
		self::$space	= $space;
		self::$release		= $release;

		self::setVar('release', $release);
		self::setVar('space', $space);

		deploymentQueue::init($space, $plan, $release);

		$xmlTasks		= self::$document->xpath("/project/space[@name='{$space}']/plan[@name='{$plan}']/task");

		foreach($xmlTasks as $xmlTask)
		{
			if ((int)$xmlTask['ignore'])
			{
				continue;
			}

			$task = deploymentTaskFactory::create($xmlTask, $space, $plan, $release);
			if ($task)
			{
				$task->prepare();
			}
		}
	}

	static function getReleases()
	{
		$dh = opendir(self::parse('${system::plan}'));

		while (($file = readdir($dh)) !== false)
		{
			if ($file == 'conf')
			{
				continue;
			}
			
			if (is_dir(self::parse('${system::plan}') . '/' . $file) && $file[0] != '.')
			{
				$info = pathinfo($file);
				$releases[] = $info['basename'];
			}
        }

        closedir($dh);

		natsort($releases);
		$releases = array_values($releases);

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

	static function getHost($alias)
	{
		if (strpos($alias, 'func:') === false)
		{
			$hosts[] = self::parse($alias);
		}
		else
		{
			$alias	=	preg_match('|\${func:(\w+)::(\w+)\(\)}|', $alias, $matches);
			$hosts	=	call_user_func(array($matches[1], $matches[2]));
		}

		if ($hosts) foreach($hosts as &$host)
		{
			if ($host)
			{
				$host = 'ssh -o StrictHostKeyChecking=no -o UserKnownHostsFile=/dev/null -o LogLevel=ERROR www-data@' . $host;
			}
			else
			{
				unset($host);
			}
		}
		
		return $hosts;
	}

	static function match_constants($matches)
	{
		return constant($matches[1]);
	}

	static function match_vars($matches)
	{
		if ($matches[2] == '::')
		{
			return self::getVar($matches[3], $matches[1]);
		}

		return self::getVar($matches[1]);
	}

	static function parse($alias)
	{
		$alias = preg_replace_callback('|\${const:(\w*)}|', array(self, 'match_constants'), $alias);
		$alias = preg_replace_callback('|\${(\w*)(::){0,1}([a-zA-Z0-9\.\-]*)}|', array(self, 'match_vars'), $alias);

		return $alias;
	}

	static function getReleaseDir($release)
	{
		return self::parse('release', self::$space) . '/' . release;
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

