<?php

class application
{

	static public function init()
	{
		session	::	init();
        log		::	init();
        mc		::	init();
		auth	::	init();
		request	::	init();
		head	::	init();

		event::process(event::EVENT_BEFORE_ACL);
		acl		::	init();
	}

	/**
	 * @static
	 *
	 */
	static public function run()
	{
		application::init();

        try
        {
			acl::check();
			event::process(event::EVENT_BEFORE_CONTROLLER);
			application::dispatch(request::module(), request::action());
			event::process(event::EVENT_BEFORE_RENDER);
        }
		catch (PDOException $e)
		{
            log::critical($e->getMessage(), array(
                'info'  =>  $e->errorInfo,
                'trace' =>  $e->getTraceAsString()
            ), 'mysql');

			response::exception('Database Error');
			application::dispatch('exception', 'database', $e);
		}
		catch (accessDeniedException $e)
		{
			application::dispatch('exception', 'accessDenied', $e);
		}
        catch (controllerException $e)
        {
			stack::clear();
            application::dispatch('exception', 'controller', $e);
        }
		catch (badResourceException $e)
		{
			application::dispatch('exception', 'badResource', $e);
		}
		catch (forwardException $e)
		{
			application::dispatch($e->module, $e->action, $e->data, $e->actionVars);
		}

		if ($layout = render::getLayout())
		{
			jquery::init('html');
			stack::push(application::controller($layout[0], $layout[1])->dispatch(request::get()), 'layout');
			render::stack('layout');
		}
		else
		{
			render::stack();
		}
	}

	/**
	 * @static
	 * @param $module
	 * @param string $action
	 * @param bool $data
	 * @param bool $application
	 */
    static public function dispatch($module, $action = 'index', $data = false, $actionVars = false, $stack = 'default')
    {
		try
		{
			return stack::push(application::controller($module, $action)->dispatch($data ? $data : request::get(), $actionVars), $stack);
		}
		catch (forwardException $e)
		{
			application::dispatch($e->module, $e->action, $e->data, $e->actionVars);
		}

    }

	/**
	 * @static
	 * @param $module
	 * @param $task
	 * @param bool $data
	 * @return mixed
	 */
    static public function execute($module, $task, $data = false)
    {
		log		::	init();
		mc		::	init();

        try
        {
            $context['controller']  = application::controller($module, $task);

            if (!$data)
            {
                $context['data'] = $_SERVER['argv'];
            }
            else
            {
                $context['data']        = $data;
            }

			foreach ($context['data'] as $arg)
			{
				if (substr($arg, 0, 2) === '--')
				{
					list($param, $value)	=	explode("=", $arg, 2);

					$params[substr($param, 2)]	=	$value;

				}
				else
				{
					$params[]	=	$arg;
				}
			}

            $context['module'] = $module;
            $context['action'] = $task;

            $code = $context['controller']->dispatch($params);

        }
		catch (PDOException $e)
		{
            log::critical($e->getMessage(), array(
                'info'  =>  $e->errorInfo,
                'trace' =>  $e->getTraceAsString()
            ), 'mysql');

            echo "\n********************\n";
            echo "DB Exception: " . $e->getMessage();
            echo "\n" . $e->getTraceAsString();
            echo "\n********************\n";

		}
        catch (controllerException $e)
        {
            echo "\n********************\n";
            echo "Exception: " . $e->getMessage();
            echo "\n" . $e->getTraceAsString();
            echo "\n********************\n";
        }

        return $code;
    }

	/**
	 *
	 * @param $module module name
	 * @param $action action name
	 * @return actionController controller
	 */
    public static function controller($module, $action = 'index')
    {
		$controller = router::get($action . ucfirst($module) . 'Controller');

        if (!$controller)
        {
			response::code(404);

			if (router::get('_404LayoutController'))
			{
				throw new forwardException('layout', '_404');
			}
			else
			{
				throw new controllerException(sprintf("Action %s:%s not found", $module, $action));
			}
    	}

        $actionClassName = $action . ucfirst($module) . 'Controller';

        return new $actionClassName($module, $action);
    }
}

