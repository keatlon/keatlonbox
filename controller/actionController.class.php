<?php

abstract class actionController
{
	const		SUCCESS		= 'success';
	const		ERROR		= 'error';
	const		EXCEPTION	= 'exception';

	public		$response		=	false;
	private		$moduleName		=	false;
	private		$actionName		=	false;

	private		$actionVars	=	array();
	
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
		if (!isset($this->$name) || isset($this->actionVars[$name]))
		{
			$this->actionVars[$name] = $value;

		}
		else
		{
			$this->$name = $value;
		}
	}

	function & __get($name)
	{
		if (!isset($this->$name) || isset($this->actionVars[$name]))
		{
			return $this->actionVars[$name];
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

	function getActionVars()
	{
		return $this->actionVars;
	}

	function setActionVars($vars)
	{
		$this->actionVars = $vars;
	}

}

