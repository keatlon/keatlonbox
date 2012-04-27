<?php

class stack
{
	protected static $currentModule =	false;
	protected static $stacks 		=	array();
	protected static $partials 		=	array();

	static function push(webActionController $action, $stack = 'default')
	{
		self::$stacks[$stack][] = $action;
	}

	static function partial($template, $vars, $stack = 'default')
	{
		self::$stacks[$stack][]	=	partial::render($template, $vars);
	}

	static function render($stack = 'default')
	{
		if (!self::$stacks[$stack])
		{
			return false;
		}

		$renderer	=	rendererFactory::create(application::getRenderer());

		foreach(self::$stacks[$stack] as $controller)
		{
			if ($controller instanceof webActionController)
			{
				self::currentModule($controller->getModuleName());
				$renderer->render($controller);
			}
			else
			{
				echo $controller;
			}
		}
	}

	/**
	 *
	 * @static
	 * @return actionController
	 */
	static function last($name = 'default')
	{
		return self::$queue[count(application::$queue) - 1];
	}

	static function hasController($module, $action = false, $stack = 'default')
	{
		if (!self::$stacks[$stack])
		{
			return false;
		}

		foreach(self::$stacks[$stack] as $controller)
		{

			if ($action)
			{
				if ($controller->getModuleName() == $module && $controller->getActionName() == $action)
				{
					return true;
				}
			}
			else
			{
				if ($controller->getModuleName() == $module)
				{
					return true;
				}
			}

		}

		return false;
	}

	static function currentModule($module = false)
	{
		if ($module)
		{
			self::$currentModule = $module;
		}

		return self::$currentModule;
	}

	static function getLastController($stack = 'default')
	{
		if (!self::$stacks[$stack])
		{
			return false;
		}

		return self::$stacks[$stack][count(self::$stacks[$stack]) - 1];
	}

}