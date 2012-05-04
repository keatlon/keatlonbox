<?php

class render
{
	const XML		= 1;
	const JSON		= 2;
	const DIALOG	= 4;

	protected static $layout 	= array('layout', 'index');
	protected static $type 		= self::XML;

	public static function getLayout()
	{
		return self::$layout;
	}

	public static function setLayout($module = false, $action = false)
	{
		self::$layout	=	$module ? array($module, $action) : false;
		return self::$layout;
	}

	public static function type($type = false)
	{
		if ($type)
		{
			stack::clear();
			self::$type	=	$type;
		}

		return self::$type;
	}

	public static function stack($stack = 'default')
	{
		stack::render($stack);
	}

	public static function controller(webActionController $controller)
	{
		$controller->beforeRender();

		switch($controller->render())
		{
			case self::XML:
				self::xml($controller);
				break;

			case self::DIALOG:
				self::dialog($controller);
				break;

			case self::JSON:
				self::json($controller);
				break;
		}

		$controller->afterRender();

	}

	/**
	 * Render specific template
	 *
	 * @static
	 *
	 * @param       $__template__
	 * @param array $__vars__
	 * @param bool  $__return__
	 *
	 * @return string
	 */
	public static function partial($__template__, $__vars__ = array(), $__return__ = true)
	{
		if ($__return__)
		{
			ob_start();
		}

		extract($__vars__, EXTR_OVERWRITE);
		include self::getTemplatePath($__template__);

		if ($__return__)
		{
			$__template__ = ob_get_contents();
			ob_end_clean();
			return $__template__;
		}
	}

	protected static function dialog(actionController $__controller__)
	{
		response::set('status', $__controller__->__response['code']);
		response::set('data', array());

		ob_start();
		self::xml($__controller__);
		response::set('body', ob_get_contents());
		ob_end_clean();

		echo json_encode(response::get());

	}

	protected static function json(actionController $__controller__)
	{
		response::set('status', $__controller__->__response['code']);

		if ($__controller__->__response['code'] == actionController::SUCCESS)
		{
			response::set('data', $__controller__->getActionVars());
		}


		if (request::get('KBOX_REQUEST_SRC') == 'iframe')
		{
			echo '<textarea>' . json_encode(response::get()) . '</textarea>';
		}
		else
		{
			echo json_encode(response::get());
		}
	}

	protected static function xml(actionController $__controller__)
	{
		extract($__controller__->getActionVars(), EXTR_OVERWRITE);
		require self::getTemplatePath($__controller__->getActionName(), $__controller__->getModuleName());
	}

	/**
	 * Get path to template
	 *
	 * @static
	 * @param $action
	 * @param bool $module
	 * @return string
	 */
	static protected function getTemplatePath($action, $module = false)
	{
		if (substr($action, 0, 2) == '//')
		{
			return conf::$conf['rootdir'] . substr($action, 1) . '.view.php';
		}

		if ($action[0] == '/')
		{
			return conf::$conf['rootdir'] . '/apps' . $action . '.view.php';
		}

		if (!$module)
		{
			$module = stack::currentModule();
		}

		return conf::$conf['rootdir'] . '/apps/' . APPLICATION . '/' . $module . '/view/' . $action . '.view.php';
	}

}