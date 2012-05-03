<?php

abstract class actionController
{
	const		SUCCESS			=	'success';
	const		ERROR			=	'error';
	const		EXCEPTION		=	'exception';

	public		$__response		=	false;
	private		$__moduleName		=	false;
	private		$__actionName		=	false;
	private		$__actionVars	=	array();
	
	function __construct($moduleName, $actionName)
	{
		$this->__moduleName = $moduleName;
		$this->__actionName = $actionName;
		$this->__response['code'] = actionController::SUCCESS;
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
		if (!isset($this->$name) || isset($this->__actionVars[$name]))
		{
			$this->__actionVars[$name] = $value;
		}
		else
		{
			$this->$name = $value;
		}
	}

	function & __get($name)
	{
		if (!isset($this->$name) || isset($this->__actionVars[$name]))
		{
			return $this->__actionVars[$name];
		}
		else
		{
			return $this->$name;
		}
	}

	function getModuleName()
	{
		return $this->__moduleName;
	}
	
	function getActionName()
	{
		return $this->__actionName;
	}

	function getActionVars()
	{
		return $this->__actionVars;
	}

	function setActionVars($vars)
	{
		$this->__actionVars = $vars;
	}
}

