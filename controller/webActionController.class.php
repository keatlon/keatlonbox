<?php
abstract class webActionController extends actionController
{
	public		$response		=	false;
	private		$viewName		=	false;

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
				$this->response['code'] = $code;
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
			log::exception($e);
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

	public function setView($view)
	{
		$this->viewName = $view;
	}

	public function get($request)
	{

	}

	public function post($request)
	{

	}

	function setTitle($title)
	{
		response::set('title', $title);
	}

	function setNotice($notice)
	{
		response::set('notice', $notice);
	}

	function setWarning($warning)
	{
		response::set('warning', $warning);
	}

	function setErrors($errors)
	{
		response::set('errors', $errors);
		return self::ERROR;
	}

	function setError($field, $message)
	{
		$errors	=	response::get('errors');
		$errors[$field]	=	$message;
		response::set('errors', $errors);
		return self::ERROR;
	}

	function forward($module , $action = 'index')
	{
		throw new forwardException($module, $action, $data);
	}

	function render($view = false, $renderer = false)
	{
		if (!$renderer)
		{
			$renderer	=	request::accept();
		}

		rendererFactory::create($renderer)->render($this, $view);
	}

}
