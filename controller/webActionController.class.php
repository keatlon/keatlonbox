<?php
abstract class webActionController extends actionController
{
	public		$__response		=	false;
	public		$__forwarded	=	false;
	private		$__render		=	render::XML;

	function __construct($moduleName, $actionName)
	{
		$this->render(render::type());
		parent::__construct($moduleName, $actionName);
	}

	public function dispatch($data)
	{
		try
		{
			$this->beforeExecute();
			
			if (request::method() == request::POST)
			{
				$code = $this->post($data);
			}

			if (request::method() == request::GET)
			{
				$code = $this->get($data);
			}
			
			if ($code)
			{
				$this->__response['code'] = $code;
			}

			$this->afterExecute();
		}
		catch (redirectException $e)
		{
			$this->afterExecute();
		}
		catch (forwardException $e)
		{
			application::dispatch($e->module, $e->action, $data);
			return self::EXCEPTION;
		}
		catch (dbException $e)
		{
			application::dispatch('exception', 'database', $e);
			return self::EXCEPTION;
		}
		catch (badResourceException $e)
		{
			application::dispatch('exception', 'badResource', $e);
			return self::EXCEPTION;
		}
		catch (accessDeniedException $e)
		{
			application::dispatch('exception', 'accessDenied', $e);
			return self::EXCEPTION;
		}
		catch (Exception $e)
		{
			log::exception($e);
			application::dispatch('exception', 'application', $e);
			return self::EXCEPTION;
		}

		return $this;
	}

    public function afterExecute()
    {
	}

	public function beforeRender()
	{

	}

	public function afterRender()
	{

	}

	public function get($request)
	{

	}

	public function post($request)
	{

	}

	function forward($module , $action = 'index', $data = array())
	{
		$this->__forwarded = true;
		throw new forwardException($module, $action, $data);
	}

	function dialog($module , $action = 'index')
	{
		request::method(request::GET);
		return $this->forward($module , $action);
	}


	function render($type = false)
	{
		if ($type)
		{
			$this->__render	=	$type;
		}

		return $this->__render;
	}

	function view($view)
	{

	}
}
