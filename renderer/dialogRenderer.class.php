<?php
class dialogRenderer extends baseRenderer
{
	public function render(actionController $__action, $__view = false)
	{
		$__action->beforeRender();

		$data	=	array();
		response::set('status', $__action->response['code']);
		response::set('data', $data);

		ob_start();
		$__action->render($__view, rendererFactory::HTML);
		response::set('body', ob_get_contents());
		ob_end_clean();

		echo json_encode(response::get());

		$__action->afterRender();
	}
}
