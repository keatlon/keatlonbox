<?php
class jsonRenderer extends baseRenderer
{
	public function render(actionController $__action, $__view = false)
	{
		$__action->beforeRender();

		$data	=	array();
		response::set('status', $__action->response['code']);
		
		if ($__action->response['code'] == actionController::SUCCESS)
		{
			$__actionVars	=	$__action->getActionVars();

			if ($__actionVars) foreach($__actionVars as $var_name => $var_value)
			{
				$data[$var_name] = $var_value;
			}
		}

		response::set('data', $data);

		if (request::get('KBOX_REQUEST_SRC') == 'iframe')
		{
			echo '<textarea>' . json_encode(response::get()) . '</textarea>';
		}
		else
		{
			echo json_encode(response::get());
		}


		$__action->afterRender();
	}
}
