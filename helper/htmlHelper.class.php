<?php

class htmlHelper
{
    static function selected($current, $value, $class = 'selected')
    {
        return (bool)($current == $value) ? $class : '';
    }

	static function selectedModuleAction($module, $action, $extra = true)
	{
		if ($extra && $module == application::getLastAction()->getModuleName() && $action == application::getLastAction()->getActionName())
		{
			return 'selected';
		}

		return '';
	}

}