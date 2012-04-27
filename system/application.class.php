<?php

class application
{
	/**
	 * @var actionController
	 */
	static protected	$layout		=	false;
    static protected	$events		=	array();
	static protected 	$renderer	=	false;
    static public		$name		=	null;
    static public		$i18n		=	null;

	const	EVENT_BEFORE_CONTROLLER		= 0;
	const	EVENT_BEFORE_ACL 			= 1;
	const	EVENT_BEFORE_RENDER			= 2;

	static public function init()
	{
		foreach (conf::i()->phpini as $key => $value)
		{
			ini_set($key, $value);
		}

        application::$name	= APPLICATION;

		session	::	init();
        log		::	init();
        mc		::	init();
		i18n	::	init();
		auth	::	init();
		request	::	init();
		head	::	init();

		application::processEvent(application::EVENT_BEFORE_ACL);
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
				$event->handle(request::get());
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


		switch(self::getRenderer())
		{
			case	rendererFactory::HTML:

				if (1)
				{
					jquery::init('html');
					stack::push(actionControllerFactory::create('layout', 'index')->dispatch(request::get()), 'layout');
					application::render('layout');
				}
				else
				{
					application::render();
				}
				break;

			case	rendererFactory::DIALOG:
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
    
    static public function dispatch($module, $action = 'index', $data = false, $application = false)
    {
		if ($application)
		{
			application::$name = $application;
		}

		application::push( actionControllerFactory::create($module, $action) )->dispatch($data ? $data : request::get());
    }

	static function push(actionController $actionController)
	{
		if ($actionController instanceof webActionController)
		{
			stack::push($actionController);
		}

		return $actionController;
	}

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


	/**
	 * @static
	 * @return actionController
	 */
	static function getLayoutAction()
	{
		return application::$layout;
	}

	static function render($stack = 'default')
	{
		stack::render($stack);
	}

	static function setRenderer($renderer)
	{
		self::$renderer	=	$renderer;
	}

	static function getRenderer()
	{
		return self::$renderer;
	}
}

