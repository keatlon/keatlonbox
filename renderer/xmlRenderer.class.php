<?php
class xmlRenderer extends baseRenderer
{
	public function render(actionController $__action, $__view = false)
	{
        $__action->beforeRender();

        if (!$this->assigned && $__action->actionVars)
        {
            foreach($__action->actionVars as $var_name => $var_value)
            {
                $$var_name = $var_value;
            }

            $this->assigned = true;
        }

        if (!$__view)
        {
            if ($__action->viewName)
            {
                $__view = $__action->viewName;
            }
            else
            {
                $__view = $__action->getActionName();
            }
        }

        $path = baseRenderer::getTemplatePath($__view);

        if (!file_exists($path))
        {
            $actionPath = router::get(get_class($__action));
            $path = substr($actionPath, 0, strpos($actionPath, '/action/')) . '/view/' . $__view . '.view.php';
        }

        include $path;

        $__action->afterRender();
	}
}
