<?php
abstract class webActionController extends actionController
{
	public		$renderer	=	rendererFactory::HTML;
	public		$response	=	false;

	public		$loginRequired	= true;
	private		$viewName		= false;

	public function dispatch($data)
	{
		try
		{
			if ($this->renderer == rendererFactory::BASE && conf::i()->application[application::$name]['renderer'])
			{
				$this->renderer	= conf::i()->application[application::$name]['renderer'];
			}
			elseif ($this->renderer == rendererFactory::HTML)
			{
				
				if ( http::$response['accept'] == 'text/html')
				{
					$this->renderer	= rendererFactory::HTML;
				}

				if ( http::$response['accept'] == 'application/json')
				{
					$this->renderer	= rendererFactory::JSON;
				}

				if ( http::$response['accept'] == 'application/xml')
				{
					$this->renderer	= rendererFactory::XML;
				}
			}
			
			if ($this->loginRequired && !auth::hasCredentials())
			{
				throw new loginRequiredException;
			}

			$this->beforeExecute();

			$this->response['method'] = http::$method;

			if (http::$method == 'POST')
			{
				$this->put($data);
			}

			if (http::$method == 'GET')
			{
				$this->get($data);
			}

			if ($response['code'])
			{
				$this->response['code'] = $response['code'];
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
			$this->response['errors']       = $e->getMessage();
			application::dispatch('exception', 'badResource', $e);
			return self::EXCEPTION;
		}
		catch (loginRequiredException $e)
		{
			$this->response['errors']       = $e->getMessage();
			application::dispatch('exception', 'loginRequired', $e);
			return self::EXCEPTION;
		}
		catch (accessDeniedException $e)
		{
			$this->response['errors']       = $e->getMessage();
			application::dispatch('exception', 'accessDenied', $e);
			return self::EXCEPTION;
		}
		catch (Exception $e)
		{
			log::exception($e);
			$this->response['errors']       = $e->getMessage();
			application::dispatch('exception', 'application', $e);
			return self::EXCEPTION;
		}

		return $this;
	}

    public function afterExecute()
    {
		application::$stack->push($this, 'wide', $this->viewName);
	}

	public function forward($module , $action = 'index')
	{
		throw new forwardException($module, $action, $data);
	}

	public function push($view = false, $stackName = 'wide', $priority = 10, $renderer = false)
	{
		application::$stack->push($this, $stackName, $view, $priority, $renderer);
	}

	public function render($view = false)
	{
		$renderer = rendererFactory::create($this->renderer);
		$renderer->render($this, $view);
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

	public function put($request)
	{

	}

	public function javascript($variable, $value, $useKeys = false)
	{
		return application::$stack->javascript($variable, $value, $useKeys);
	}

	function setTitle($title)
	{
		$this->response['title'] = $title;
	}

	function setNotice($notice)
	{
		$this->response['notice'] = $notice;
	}

	function setLayout($layout = 'index')
	{
		application::setContext('layout', $layout);
	}

	function setErrors($errors)
	{
		$this->response['errors']	=	$errors;
		$this->response['code']		=	self::ERROR;
	}

	function setError($field, $message)
	{
		$this->response['errors'][$field]	=	$message;
		$this->response['code']		=	self::ERROR;
	}

}
?>