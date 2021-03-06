<?php

class response
{
	const		RESPONSE_OK			=	200;
	const		RESPONSE_ERROR		=	201;
	const		RESPONSE_EXCEPTION	=	500;

	protected	static	$code		=	response::RESPONSE_OK;
	protected	static	$js			=	false;
	protected	static	$response	=	array();

	static function code($value = false)
	{
		if ($value)
		{
			self::$code = $value;

			if ($value == 404)
			{
				header('HTTP/1.0 404 Not Found');
			}
		}

		return self::$code;
	}

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

		$controller		=	stack::last();

		$jsDispatcher	=	$controller->getActionName() .
							ucfirst($controller->getModuleName()) .
							'Controller' .
							ucfirst(strtolower(request::method()));

		$layoutDispatcher	=
							conf::$conf['application'][APPLICATION]['default']['layout'][1] .
							ucfirst(conf::$conf['application'][APPLICATION]['default']['layout'][0]) .
							'Controller' .
							ucfirst(strtolower(request::method()));

		self::$response['application']	=	array
		(
			'module'	=>	$controller->getModuleName(),
			'action'	=>	$controller->getActionName(),
			'renderer'	=>	render::format(),
			'js'		=>	array
			(
				'dispatchers'	=>	array($layoutDispatcher, $jsDispatcher),
				'selectors'		=>	jquery::getSelectors(),
				'commands'		=>	jquery::getCommands(),
				'callbacks'		=>	jquery::getCallbacks(),
			)
		);

		self::$response['vars']	=	jquery::getVariables();

		return self::$response;
	}

	static function redirect($url, $permanent = false)
	{
		if(request::isJson())
		{
			render::setLayout(false);
			response::set('redirect', $url);
			throw new redirectException;
		}

		if ($permanent)
		{
			header("HTTP/1.1 301 Moved Permanently");
		}

		response:header('Location:' . $url);

		exit;
	}

	static function header($header)
	{
		Header($header);
	}

	static function title($text)
	{
		self::set('title', $text);
	}

	static function notice($text)
	{
		self::set('notice', $text);
	}

	static function warning($text)
	{
		self::set('warning', $text);
	}

	static function errors($errors)
	{
		self::set('errors', $errors);
		self::code(self::RESPONSE_ERROR);
	}

	static function error($field, $message)
	{
		$errors	=	self::get('errors');
		$errors[$field]	=	$message;
		self::set('errors', $errors);
		self::code(self::RESPONSE_ERROR);
	}

	static function exception($message)
	{
		self::set('exception', $message);
		self::code(self::RESPONSE_EXCEPTION);
	}
}
