<?php
abstract class webActionController extends actionController
{
	public		$__forwarded	=	false;

	public function dispatch($data)
	{
		try
		{
			$this->beforeExecute();
			
			if (request::method() == request::POST)
			{
				$this->post($data);
			}

			if (request::method() == request::GET)
			{
				$this->get($data);
			}
			
			$this->afterExecute();
		}
		catch (redirectException $e)
		{
			$this->afterExecute();
		}
		catch (forwardException $e)
		{
			return application::dispatch($e->module, $e->action, $data);
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

	function view($view)
	{

	}
}
