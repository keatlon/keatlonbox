<?php

abstract class actionController
{
	private		$__moduleName		=	false;
	private		$__actionName		=	false;
	protected	$__actionVars		=	array();
	protected	$__format			=	false;
	protected	$__stream			=	false;

	function __construct($moduleName, $actionName)
	{
		$this->__moduleName =	$moduleName;
		$this->__actionName =	$actionName;
		$this->__format 	=	$this->__format ? $this->__format : render::format();
		$this->__stream 	=	$this->__stream ? $this->__stream : render::stream();
	}

	public function dispatch($data, $actionVars = false)
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

	function format($format = false)
	{
		if ($format)
		{
			$this->__format = $format;
		}

		return $this->__format;
	}

	function stream($stream = false)
	{
		if ($stream)
		{
			$this->__stream = $stream;
		}

		return $this->__stream;
	}
}

