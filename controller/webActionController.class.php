<?php
abstract class webActionController extends actionController
{
	public		$__forwarded	=	false;
	protected	$__data			=	array();

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
		catch (redirectException $e) {}

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

	function forward($module , $action = 'index', $method = false, $format = false)
	{
		$this->__forwarded = true;
		throw new forwardException($module, $action, $method, $format, $this->__data);
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
