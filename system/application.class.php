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
		$pid = profiler::start(profiler::APPLICATION, 'APP');

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
            application::dispatch('exception', 'webserver');
        }
        catch (moduleException $e)
        {}

		profiler::finish($pid);

		profiler::start(profiler::RENDER);

		if (render::layout())
		{
			jquery::init('html');
			stack::push(actionControllerFactory::create('layout', 'index')->dispatch(request::get()), 'layout');
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
		return stack::push(actionControllerFactory::create($module, $action) )->dispatch($data ? $data : request::get());
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
        try
        {
            $context['controller']  = actionControllerFactory::create($module, $task);

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
        catch (moduleException $e)
        {
        }

        return $code;
    }
}

