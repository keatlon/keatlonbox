<?php
class url
{
    static function parse($url)
    {
		if (conf::i()->application[application::$name]['rewrite'])
		foreach(conf::i()->application[application::$name]['rewrite'] as $pattern => $replacement)
		{
			$url = preg_replace($pattern, $replacement, $url);
		}

		$question = strpos($url, '?');

		if ($question)
		{
			$url = substr($url, 0, $question);
		}

        if ($url[0] == '/')
        {
            $url = substr($url, 1);
        }

        $parts	= explode('/', $url);

		$defaultController = conf::i()->application[application::$name]['default'];

		if (auth::hasCredentials())
		{
			$result['module'] 	= $defaultController['signedin'][0];
			$result['action'] 	= $defaultController['signedin'][1];
		}
		else
		{
			$result['module'] 	= $defaultController['signedout'][0];
			$result['action'] 	= $defaultController['signedout'][1];
		}

        if (trim($url))
		{
			$odd	= count($parts) % 2;

			if ($odd)
			{
				$module = array_shift($parts);
				$action = 'index';
			}
			else
			{
				$module = array_shift($parts);
				$action = array_shift($parts);
			}

			if (actionControllerFactory::check($module, $action))
			{
				$result['module'] 	= $module;
				$result['action'] 	= $action;
			}
			else
			{
				if ($odd)
				{
					array_push($parts, $module);
				}
				else
				{
					array_push($parts, $module);
					array_push($parts, $action);
				}
			}

			$pairs	= count($parts) / 2;

			for ($l = 0; $l < $pairs; $l++)
			{
				$result[$parts[ $l * 2]] 	= $parts[ $l * 2 + 1 ];
			}
		}

        if ($_REQUEST) foreach($_REQUEST as $name => $value)
        {
            $result[$name] = $value;
        }

        return $result;
    }
}
?>
