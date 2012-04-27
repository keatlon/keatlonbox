<?
class forwardException extends applicationException
{
	public function __construct( $module, $action, $method = false, $render = false)
	{
		$this->module = $module;
		$this->action = $action;

		if ($method)
		{
			request::method($method);
		}

		if ($render)
		{
			render::type($render);
		}

	}
}