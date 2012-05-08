<?
class forwardException extends applicationException
{
	public function __construct( $module, $action, $method = false, $render = false, $data = array())
	{
		$this->module 	=	$module;
		$this->action 	=	$action;
		$this->data 	=	$data;

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