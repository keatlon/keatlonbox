<?php

class htmlHelper
{
    static function selected($current, $value, $class = 'selected')
    {
        return (bool)($current == $value) ? $class : '';
    }

	static function selectedModuleAction($module, $action = false, $extra = true)
	{
		if ($extra && stack::hasController($module, $action))
		{
			return 'selected';
		}

		return '';
	}

}