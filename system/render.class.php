<?php

class render
{
	const XML		= 1;
	const JSON		= 2;
	const DIALOG	= 4;

	protected static $layout 	= true;
	protected static $type 		= self::XML;

	public static function layout($layout = null)
	{
		if (isset($layout))
		{
			self::$layout	=	$layout;
		}

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
		if ($__vars__) {
			foreach ($__vars__ as $__name__ => $__value__)
			{
				$$__name__ = $__value__;
			}
		}

		if ($__return__)
		{
			ob_start();
		}

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
		response::set('status', $__controller__->response['code']);
		response::set('data', array());

		ob_start();
		self::xml($__controller__);
		response::set('body', ob_get_contents());
		ob_end_clean();

		echo json_encode(response::get());

	}

	protected static function json(actionController $__controller__)
	{
		$data = array();
		response::set('status', $__controller__->response['code']);

		if ($__controller__->response['code'] == actionController::SUCCESS)
		{
			$__controller__vars = $__controller__->getActionVars();

			if ($__controller__vars) {
				foreach ($__controller__vars as $var_name => $var_value)
				{
					$data[$var_name] = $var_value;
				}
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
	}

	protected static function xml(actionController $__controller__, $__view__ = false)
	{
		/*
		if ($__controller__->isLayout())
		{
		$__controller__->setActionVars(array_merge((array)$__controller__->getActionVars(), (array)application::getLastAction()->getActionVars()));
		}
		else
		{
		if (application::getLayoutAction())
		{
		$__controller__->setActionVars(array_merge((array)$__controller__->getActionVars(), (array)application::getLayoutAction()->getActionVars()));
		}
		}
		*/

		$__vars__ = $__controller__->getActionVars();

		foreach ($__vars__ as $var_name => $var_value)
		{
			$$var_name = $var_value;
		}

		if (!$__view__)
		{
			if ($__controller__->viewName)
			{
				$__view__ = $__controller__->viewName;
			}
			else
			{
				$__view__ = $__controller__->getActionName();
			}
		}

		$path = self::getTemplatePath($__controller__->getActionName(), $__controller__->getModuleName());

		if (!file_exists($path))
		{
			$actionPath = router::get(get_class($__controller__));
			$path       = substr($actionPath, 0, strpos($actionPath, '/action/')) . '/view/' . $__view__ . '.view.php';
		}

		include $path;
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

		return conf::i()->rootdir . '/apps/' . APPLICATION . '/' . $module . '/view/' . $action . '.view.php';
	}

}