<?php
class baseRenderer
{
    public $assigned = false;

    public function render(actionController $action, $view = false)
    {
    }

    static public function getTemplateByAction($action)
    {
		$module		=	$action->moduleName;
		$template	=	$action->actionName;

		return self::getTemplatePath($template, $module);
	}

    static public function getTemplatePath($template, $module = false)
    {
        if (!$module)
        {
			$module = application::getLastAction()->moduleName;
        }

        if ( substr($template, 0, 2) == '//')
        {
            $partialPath = conf::i()->rootdir . substr($template, 1) . '.view.php';
        }
        else
        if ($template[0] == '/')
        {
            $partialPath = conf::i()->rootdir . '/apps' . $template . '.view.php';
        }
        else
        {
            $partialPath = conf::i()->rootdir . '/apps/' . application::$name . '/' . $module . '/view/' . $template  . '.view.php';
        }

        return $partialPath;
    }

}

?>
