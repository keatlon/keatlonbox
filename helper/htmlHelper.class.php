<?php

class htmlHelper
{
    static function selected($current, $value)
    {
        return (bool)($current == $value) ? 'selected' : '';
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