<?php
class request
{
	const	GET				=	'GET';
	const	POST			=	'POST';

	protected	static	$method		=	false;
	protected	static	$accept		=	false;
	protected	static	$data		= false;

	static function fileExists()
	{
		return !empty($_FILES);
	}

	static function file($name)
	{
		return $_FILES[$name];
	}

	static function method($method = false)
	{
		if ($method)
		{
			self::$method	=	$method;
		}
		
		return self::$method;
	}

	static function accept($type = false)
	{
		if ($type)
		{
			self::$accept	=	$type;
		}

		return self::$accept;
	}

	protected static function set($key, $value)
	{
		self::$data['params'][$key] = $value;
	}

	protected static function data($data)
	{
		self::$data = $data;
	}

	static function get($key = false)
	{
		if($key)
		{
			return self::$data['params'][$key];
		}

		return self::$data['params'];

	}

	static function module()
	{
		return self::$data['module'];
	}

	static function action()
	{
		return self::$data['action'];
	}

	static public function init()
	{
		self::method($_SERVER['REQUEST_METHOD']);
		self::data(url::parse($_SERVER['REQUEST_URI']));

		if (!$_SERVER['HTTP_KBOX_RENDERER'] && (self::isPost() || $_FILES || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false))
		{
			$_SERVER['HTTP_KBOX_RENDERER'] = 'json';
		}

		switch($_SERVER['HTTP_KBOX_RENDERER'])
		{
			case 'json':
				render::setLayout(false);
				render::type(render::JSON);
				break;

			case 'dialog':
				render::setLayout(false);
				render::type(render::DIALOG);
				break;

			case 'xml':
			default:
				render::type(render::XML);
		}

	}

	static function isPost()
	{
		return (self::method() == 'POST');
	}

	static function isJson()
	{
		return (render::type() == render::JSON);
	}

	static function raw()
	{
		return file_get_contents('php://input');
	}

}
