<?php
class url
{
    static function parse($url)
    {
		return call_user_func(conf::$conf['application']['url']['parser'], $url);
	}

    static function build($module, $action, $params)
    {
		return call_user_func(conf::$conf['application']['url']['builder'], $module, $action, $params);
	}

    static function _parse($url)
    {
		$info				=	parse_url($url);
		$result				=	array('params' => array());

		if (preg_match_all('|[^/]+|', $info['path'], $matches))
		{
			if(count($matches[0]) % 2)
			{
				array_splice($matches[0], 1, 0, array('index'));
			}

			$result['module'] 	= array_shift($matches[0]);
			$result['action'] 	= array_shift($matches[0]);

			while($matches[0])
			{
				$result['params'][array_shift($matches[0])] = array_shift($matches[0]);
			}
		}
		else
		{
			$default = conf::$conf['application'][APPLICATION]['default'];

			if (auth::id())
			{
				$result['module'] 	= $default['signedin'][0];
				$result['action'] 	= $default['signedin'][1];
			}
			else
			{
				$result['module'] 	= $default['signedout'][0];
				$result['action'] 	= $default['signedout'][1];
			}
		}

        if ($_REQUEST) foreach($_REQUEST as $name => $value)
        {
            $result['params'][$name] = $value;
        }

        return $result;
    }

    static function _build($module, $action, $params)
    {
		$chunks	=	array();
		
		foreach($params as $key => $param)
		{
			$chunks[]	=	'/' . $key . '/' . $param;
		}

		$base	=	'/' . $module . '/' . $action;
		
		if ($chunks)
		{
			return $base . implode('', $chunks);
		}

		return $base;
	}

}
