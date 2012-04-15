<?php

class htmlHelper
{
    static function selected($current, $value, $class = 'selected')
    {
        return (bool)($current == $value) ? $class : '';
    }

	static function selectedModuleAction($module, $action = false, $extra = true)
	{
		if ($action && $extra && $module == application::getLastAction()->getModuleName() && $action == application::getLastAction()->getActionName())
		{
			return 'selected';
		}

		if (!$action && $extra && $module == application::getLastAction()->getModuleName())
		{
			return 'selected';
		}

		return '';
	}

}