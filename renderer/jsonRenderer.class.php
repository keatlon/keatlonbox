<?php
class jsonRenderer extends baseRenderer
{
	public function render(actionController $__action, $__view = false)
	{
		$__action->beforeRender();

		$data	=	array();
		response::set('status', $__action->response['code']);
		
		if (request::method() == request::POST)
		{
			if ($__action->response['code'] == actionController::SUCCESS)
			{
				$__actionVars	=	$__action->getActionVars();

				if ($__actionVars) foreach($__actionVars as $var_name => $var_value)
				{
					$data[$var_name] = $var_value;
				}
			}
		}

		response::set('data', $data);

		if (request::method() == request::GET)
		{
			ob_start();
			$__action->render($__view, rendererFactory::HTML);
			response::set('body', ob_get_contents());
			ob_end_clean();
		}

		echo json_encode(response::get());

		$__action->afterRender();
	}
}
