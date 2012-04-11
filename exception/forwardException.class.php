<?
class forwardException extends applicationException
{
	public function __construct( $module, $action, $method = false, $renderer = false)
	{
		$this->module = $module;
		$this->action = $action;

		if ($method)
		{
			request::method($method);
		}

		if ($renderer)
		{
			application::setRenderer($renderer);
		}

	}
}