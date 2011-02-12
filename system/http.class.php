<?php
class http
{
	const	GET				=	'GET';
	const	POST			=	'POST';

	static $response        = false;
	static $request         = false;
	static $files           = false;
	static $method          = false;

	static public function init()
	{
		http::$method		= $_SERVER['REQUEST_METHOD'];
		http::$request		= url::parse($_SERVER['REQUEST_URI']);
		http::$files		= $_FILES;

		if (strpos($_SERVER['HTTP_ACCEPT'], 'application/xml') !== false)
		{
			http::$response['accept'] = 'application/xml';
		}

		if (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') !== false || strpos($_SERVER['HTTP_ACCEPT'], '*/*') !== false)
		{
			http::$response['accept'] = 'text/html';
		}
		
		if (http::$files || strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)
		{
			http::$response['accept'] = 'application/json';
		}
	}

	static function getUploadedFile($field)
	{
		return http::$files[$field]['tmp_name'];
	}

	static function redirect($url, $direct = false)
	{
		if(application::getContext('controller')->renderer == 'json')
		{
			if ($direct)
			{
				application::getContext('controller')->response['redirect'] = $url;
			}
			else
			{
				application::getContext('controller')->response['jsonredirect'] = $url;
			}
			
			throw new redirectException;
		}

		Header('Location:' . $url);
		exit;
	}

	protected static function ieUpgrade()
	{
		$start = strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE');

		if( $start !== false)
		{
			$end    = strpos($_SERVER['HTTP_USER_AGENT'], ';', $start);
			$v      = substr($_SERVER['HTTP_USER_AGENT'], $start, $end - $start);
			preg_match('|(\d{1})\.(\d{1})|', $v, $matches);

			if ((int)$matches[1] <= 6 && $_REQUEST['req'] != 'error/ie')
			{
				http::redirect('/error/ie');
			}
		}
	}

}
?>