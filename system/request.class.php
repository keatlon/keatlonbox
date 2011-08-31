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
		request::method($_SERVER['REQUEST_METHOD']);

		if (conf::i()->application[application::$name]['renderer'])
		{
			request::accept(conf::i()->application[application::$name]['renderer']);
		}
		else
		{
			if (strpos($_SERVER['HTTP_ACCEPT'], 'application/xml') !== false)
			{
				request::accept(rendererFactory::XML);
			}

			if (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false || strpos($_SERVER['HTTP_ACCEPT'], '*/*') !== false)
			{
				request::accept(rendererFactory::HTML);
			}

			if ($_FILES || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
			{
				request::accept(rendererFactory::JSON);
			}
		}

		request::data(url::parse($_SERVER['REQUEST_URI']));
	}

	static function isHtml()
	{
		return (request::accept() == rendererFactory::HTML);
	}

	static function isJson()
	{
		return (request::accept() == rendererFactory::JSON);
	}

	static function isXml()
	{
		return (request::accept() == rendererFactory::XML);
	}


}
