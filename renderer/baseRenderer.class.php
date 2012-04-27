<?php
class baseRenderer
{
    public $assigned = false;

    public function render(actionController $action, $view = false)
    {
    }

    static public function getTemplatePath($action, $module = false)
    {
        if ( substr($action, 0, 2) == '//')
        {
            return conf::i()->rootdir . substr($action, 1) . '.view.php';
        }

        if ($action[0] == '/')
        {
            return conf::i()->rootdir . '/apps' . $action . '.view.php';
        }

		if (!$module)
		{
			$module = stack::currentModule();
		}

		return conf::i()->rootdir . '/apps/' . application::$name . '/' . $module . '/view/' . $action . '.view.php';
    }

}

