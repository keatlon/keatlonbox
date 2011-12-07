<?php
class htmlRenderer extends baseRenderer
{
    public function render(actionController $__action, $__view = false)
    {
        $__action->beforeRender($__action);

		if ($__action->isLayout())
		{
			$__action->setActionVars(array_merge((array)$__action->getActionVars(), (array)application::getLastAction()->getActionVars()));
		}
		else
		{
			if (application::getLayoutAction())
			{
				$__action->setActionVars(array_merge((array)$__action->getActionVars(), (array)application::getLayoutAction()->getActionVars()));
			}
		}

		$__actionVars	= $__action->getActionVars();

        if (!$this->assigned && $__actionVars)
        {
            foreach($__actionVars as $var_name => $var_value)
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
