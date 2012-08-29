<?
class forwardException extends applicationException
{
	public function __construct( $module, $action, $method = false, $format = false, $data = array(), $actionVars = array())
	{
		$this->module 		=	$module;
		$this->action 		=	$action;
		$this->data 		=	$data;
		$this->actionVars 	=	$actionVars;

		if ($method)
		{
			request::method($method);
		}

		if ($format)
		{
			render::format($format);
		}

	}
}