<?php

class application
{

	static public function init()
	{
		foreach (conf::$conf['phpini'] as $key => $value)
		{
			ini_set($key, $value);
		}

		session	::	init();
        log		::	init();
        mc		::	init();
		i18n	::	init();
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
		catch (dbException $e)
		{
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
			application::dispatch($e->module, $e->action, $e->data);
		}
		catch (Exception $e)
		{
			log::exception($e);
			application::dispatch('exception', 'application', $e);
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
    static public function dispatch($module, $action = 'index', $data = false)
    {
		return stack::push(application::controller($module, $action)->dispatch($data ? $data : request::get()));
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
		foreach (conf::$conf['phpini'] as $key => $value)
		{
			ini_set($key, $value);
		}

		i18n	::	init();
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

            $context['module'] = $module;
            $context['action'] = $task;

            $code = $context['controller']->dispatch($context['data']);
        }
        catch (controllerException $e)
        {
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
            throw new controllerException('/' . $module . '/' . $action . ' does not exist');
    	}

        $actionClassName = $action . ucfirst($module) . 'Controller';

        return new $actionClassName($module, $action);
    }
}

