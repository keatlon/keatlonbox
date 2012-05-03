<?php

class stack
{
	protected static $currentModule =	false;
	protected static $stacks 		=	array();

	/**
	 * Add controller to rendering stack
	 *
	 * @static
	 * @param webActionController $action
	 * @param string $stack
	 * @return array
	 */
	static function push(webActionController $action, $stack = 'default')
	{
		if ($action->__forwarded)
		{
			return false;
		}

		self::$stacks[$stack][] = $action;
		return $action;
	}

	/**
	 * Add rendered template to stack
	 *
	 * @static
	 * @param $template
	 * @param $vars
	 * @param string $stack
	 */
	static function partial($template, $vars, $stack = 'default')
	{
		self::$stacks[$stack][]	=	render::partial($template, $vars);
	}

	/**
	 * Render whole stack
	 *
	 * @static
	 * @param string $stack
	 * @return bool
	 */
	static function render($stack = 'default')
	{
		if (!self::$stacks[$stack])
		{
			return false;
		}

		foreach(self::$stacks[$stack] as $controller)
		{
			if ($controller instanceof webActionController)
			{
				self::currentModule($controller->getModuleName());
				render::controller($controller);
			}
			else
			{
				echo $controller;
			}
		}
	}

	/**
	 * Get last dispatched controller
	 *
	 * @static
	 * @return actionController
	 */
	static function last($stack = 'default')
	{
		return (self::$stacks[$stack])  ? self::$stacks[$stack][count(self::$stacks[$stack]) - 1] : false;
	}

	/**
	 * Find controller in a stack by module and action name
	 *
	 * @static
	 * @param $module
	 * @param bool $action
	 * @param string $stack
	 * @return bool
	 */
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

	/**
	 * Get current rendering moudle
	 *
	 * @static
	 * @param bool $module
	 * @return bool
	 */
	static function currentModule($module = false)
	{
		if ($module)
		{
			self::$currentModule = $module;
		}

		return self::$currentModule;
	}

	static function clear($stack = 'default')
	{
		self::$stacks[$stack] = false;
	}

}