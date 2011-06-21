<?php
class htmlRenderer extends baseRenderer
{
    public function render(actionController $__action, $__view = false)
    {
        $__action->beforeRender($__action);

        if (!$this->assigned && $__action->action_vars)
        {
            foreach($__action->action_vars as $var_name => $var_value)
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
                $__view = $__action->actionName;
            }
        }

        $path = baseRenderer::getTemplateByAction($__action);

        if (!file_exists($path))
        {
            $actionPath = router::get(get_class($__action));
            $path = substr($actionPath, 0, strpos($actionPath, '/action/')) . '/view/' . $__view . '.view.php';
        }

        include $path;

        $__action->afterRender($__action);
    }
}
