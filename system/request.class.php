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

	static function ajax($is = false)
	{
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
		self::setRenderer();
		self::method($_SERVER['REQUEST_METHOD']);
		self::data(url::parse($_SERVER['REQUEST_URI']));
	}

	static function setRenderer()
	{
		switch($_SERVER['HTTP_KBOX_RENDERER'])
		{
			case 'html':
				return application::setRenderer(rendererFactory::HTML);

			case 'xml':
				return application::setRenderer(rendererFactory::XML);

			case 'json':
				return application::setRenderer(rendererFactory::JSON);

			case 'dialog':
				return application::setRenderer(rendererFactory::DIALOG);
		}

		if (conf::i()->application[application::$name]['renderer'])
		{
			return application::setRenderer(conf::i()->application[application::$name]['renderer']);
		}

		if (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false)
		{
			return application::setRenderer(rendererFactory::HTML);
		}

		if (strpos($_SERVER['HTTP_ACCEPT'], 'application/xml') !== false)
		{
			return application::setRenderer(rendererFactory::XML);
		}

		if ($_FILES || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
		{
			return application::setRenderer(rendererFactory::JSON);
		}
	}

	static function isHtml()
	{
		return (application::getRenderer() == rendererFactory::HTML);
	}

	static function isJson()
	{
		return (application::getRenderer() == rendererFactory::JSON);
	}

	static function isXml()
	{
		return (application::getRenderer() == rendererFactory::XML);
	}

	static function raw()
	{
		return file_get_contents('php://input');
	}

}
