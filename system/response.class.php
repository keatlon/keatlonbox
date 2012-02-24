<?php

class response
{
	protected	static	$js			=	false;
	protected	static	$response	=	array();

	static function set($key, $value)
	{
		self::$response[$key]	=	$value;
	}

	static function get($key = false)
	{
		if ($key)
		{
			return self::$response[$key];
		}

		$action			=	application::getLastAction();

		$jsDispatcher	=	$action->getActionName() .
							ucfirst($action->getModuleName()) .
							'Controller' .
							ucfirst(strtolower(request::method()));

		self::$response['application']	=	array
		(
			'module'	=>	$action->getModuleName(),
			'action'	=>	$action->getActionName(),
			'renderer'	=>	application::getRenderer(),
			'js'		=>	array
			(
				'dispatcher'	=>	$jsDispatcher,
				'selectors'		=>	jquery::getSelectors(),
				'commands'		=>	jquery::getCommands()
			)
		);

		self::$response['vars']				=	jquery::getVariables();

		return self::$response;
	}

	static function redirect($url, $direct = false)
	{
		if(request::isJson())
		{
			if ($direct)
			{
				response::set('redirect', $url);
			}
			else
			{
				response::set('jsonredirect', $url);
			}

			throw new redirectException;
		}

		Header('Location:' . $url);
		exit;
	}

    static function slicer()
    {
		$output = 'var slicers = {';
		if ($slicers = slicer::iterate())
		{
			foreach($slicers as $name => $slicer)
			{
				$generatedSlicers[] = "'" . $name . "' : {name: '" . $name . "', mode: '" . $slicer->mode . "', page: " . (int)$slicer->page . ", maxPage : " . (int)$slicer->maxPage . ", enableKeys: " . (int)$slicer->enableKeys . ",obj: null}";
			}
			$output .= implode(',', $generatedSlicers);
		}
		$output .= '};';

		return $output;
	}

}
