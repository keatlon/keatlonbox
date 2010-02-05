<?
class forwardException extends applicationException
{
	public function __construct( $module, $action)
	{
            $this->module = $module;
            $this->action = $action;
	}
}