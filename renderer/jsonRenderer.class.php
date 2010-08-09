<?php
class jsonRenderer extends baseRenderer
{
	public function render(actionController $__action, $__view = false)
	{
		$__action->beforeRender();

		$result['context']		= array('module' => $__action->getModuleName() , 'action' => $__action->getActionName());
		
		$result['data']		= array();
		$result['status']	= $__action->response['code'];
		
		($__action->response['errors'])		? $result['errors']		= $__action->response['errors'] : '';
		($__action->response['title'])		? $result['title']		= $__action->response['title'] : '';
		($__action->response['message'])	? $result['message']	= $__action->response['message'] : '';
		($__action->response['notice'])		? $result['notice']		= $__action->response['notice'] : '';
		($__action->response['redirect'])	? $result['redirect']	= $__action->response['redirect'] : '';


		if ($__action->response['method'] == 'POST')
		{
			if ($__action->response['code'] == actionController::SUCCESS)
			{
				if ($__action->action_vars) foreach($__action->action_vars as $var_name => $var_value)
				{
					$result['data'][$var_name] = $var_value;
				}
			}
		}

		if ($__action->response['method'] == 'GET')
		{
			ob_start();
			$__action->renderer = rendererFactory::HTML;
			$__action->render($__view);
			$result['body'] = ob_get_contents();
			ob_end_clean();
		}

		echo json_encode($result);

		$__action->afterRender();
	}
}
?>