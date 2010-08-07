<?php
abstract class actionController
{
	const		SUCCESS		= 'success';
	const		ERROR		= 'error';
	const		EXCEPTION	= 'exception';

	public		$response			= false;
	private		$moduleName		= false;
	private		$actionName		= false;

	private		$action_vars	= array();

	function __construct($moduleName, $actionName)
	{
		$this->moduleName = $moduleName;
		$this->actionName = $actionName;
		$this->response['code'] = actionController::SUCCESS;
	}

	public function dispatch($data)
	{
	}

	public function beforeExecute()
	{

	}

	public function afterExecute()
	{

	}

	function __set($name, $value)
	{
		if (!isset($this->$name) || isset($this->action_vars[$name]))
		{
			$this->action_vars[$name] = $value;

		}
		else
		{
			$this->$name = $value;
		}
	}

	function & __get($name)
	{
		if (!isset($this->$name) || isset($this->action_vars[$name]))
		{
			return $this->action_vars[$name];
		}
		else
		{
			return $this->$name;
		}
	}

	function getModuleName()
	{
		return $this->moduleName;
	}
	
	function getActionName()
	{
		return $this->actionName;
	}
}
?>