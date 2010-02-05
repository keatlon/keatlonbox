<?php
class actionControllerFactory
{
	/**
	 * 
	 * @param $module module name
	 * @param $action action name
	 * @return actionController controller
	 */
    public static function create($module, $action = 'index')
    {
        if (!actionControllerFactory::check($module, $action))
        {
            throw new controllerException();
    	}

        $actionClassName = $action . ucfirst($module) . 'Controller';
        return new $actionClassName($module, $action);
    }
	
    public static function check($module, $action)
    {
        return router::get($action . ucfirst($module) . 'Controller');
    }
}
?>