<?php

class application
{
    static protected	$queue		=	array();
    static protected	$events		=	array();
    static public		$name		=	null;
    static public		$i18n		=	null;

	const	EVENT_BEFORE_CONTROLLER		= 0;
	const	EVENT_BEFORE_RENDER			= 2;

	static public function init()
	{
		date_default_timezone_set(conf::i()->application['timezone']);
		
        application::$name	= APPLICATION;

		session	::	init();
        log		::	init();
        mc		::	init();
		i18n	::	init();
		auth	::	init();
		request	::	init();
		acl		::	init();
	}

    public static function registerEvent( $name, $position = application::EVENT_BEFORE_CONTROLLER )
    {
        $eventClassName			=	$name . 'Event';
        self::$events[ $name ]	=	new $eventClassName;
		self::$events[ $name ]->position = $position;
    }

	static public function processEvent($type)
	{
		if (application::$events) foreach(application::$events as $event)
		{
			if ($event->position == $type)
			{
				$event->handle();
			}
		}
	}

	static public function run()
	{
		ob_start();
		
		application::init();

        try
        {
			acl::check();
			application::processEvent(application::EVENT_BEFORE_CONTROLLER);
			application::dispatch(request::module(), request::action());

			application::processEvent(application::EVENT_BEFORE_RENDER);
        }
		catch (dbException $e)
		{
			log::exception($e);
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


		switch(request::accept())
		{
			case	rendererFactory::HTML:
				js::init('html');
				$layoutController  = actionControllerFactory::create('layout', 'index');
				$layoutController->dispatch(request::get());
				$layoutController->render();
				break;

			case	rendererFactory::JSON:
				application::render();
				break;

			case	rendererFactory::XML:
				header ("Content-Type:text/xml");
				echo '<?xml version="1.0" encoding="UTF-8" ?>';
				application::render();
				break;
		}
	}
    
    static public function dispatch($module, $action = 'index', $data = false)
    {
		application::push( actionControllerFactory::create($module, $action) )->dispatch($data ? $data : request::get());
    }

	static function push(actionController $actionController)
	{
		if ($actionController instanceof webActionController)
		{
			self::$queue[] = $actionController;
		}

		return $actionController;
	}

    static public function execute($module, $task, $data = false)
    {
		date_default_timezone_set(conf::i()->application['timezone']);

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

	static function getLastAction()
	{
		return application::$queue[count(application::$queue) - 1];
	}

	static function render($stack = 'default')
	{
		application::getLastAction()->render();
	}

}

