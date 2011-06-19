<?php
class request
{
	const	GET				=	'GET';
	const	POST			=	'POST';

	protected	static	$method		=	false;
	protected	static	$accept		=	false;
	protected	static	$data		= false;

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

	protected static function set($data)
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
		request::method($_SERVER['REQUEST_METHOD']);
		request::set(url::parse($_SERVER['REQUEST_URI']));

		if (strpos($_SERVER['HTTP_ACCEPT'], 'application/xml') !== false)
		{
			request::accept('application/xml');
		}

		if (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false || strpos($_SERVER['HTTP_ACCEPT'], '*/*') !== false)
		{
			request::accept('text/html');
		}
		
		if ($_FILES || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
		{
			request::accept('application/json');
		}
	}

	static function isHtml()
	{
		return (request::accept() == 'text/html');
	}

	static function isJson()
	{
		return (request::accept() == 'application/json');
	}

	static function isXml()
	{
		return (request::accept() == 'application/xml');
	}


}
?>