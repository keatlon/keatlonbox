<?php

class application
{
    static protected    $context        = array();
    static protected    $contextIndex   = 0;
    
    static public       $name       = null;
    static public       $events     = null;
	
	/**
	 * @var viewStack
	 */
    static public       $stack      = null;
    static public       $i18n       = null;
    static public       $renderer   = null;

	static public function run()
	{
        application::$name	= APPLICATION;

        if (conf::i()->debug['enable'])
        {
            define(APPLOG_ID, profiler::start(profiler::SYSTEM));
        }

		session::init();
        log::init();
        mc::init();
		i18n::init();


		foreach(conf::i()->application[APPLICATION]['auth'] as $authEngine)
		{
			if (auth::i($authEngine)->getCredentials())
			{
				auth::setGateway($authEngine);
			}
		}

		if (!auth::getGateway())
		{
			if (is_array(conf::i()->application[APPLICATION]['auth']))
			{
				auth::setGateway(conf::i()->application[APPLICATION]['auth'][0]);
			}
			else
			{
				auth::setGateway('server');
			}
			
		}

		http::init();

        try
        {

			if (application::$events) foreach(application::$events as $event)
			{
				if ($event->beforeDispatch)
				{
					$event->handle();
				}
			}

			application::dispatch(http::$request['module'], http::$request['action']);

			if (application::$events) foreach(application::$events as $event)
			{
				if ($event->afterDispatch)
				{
					$event->handle();
				}
			}

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

		if (self::$renderer == rendererFactory::HTML)
		{
			$context['data']['module'] = 'layout';
			$context['data']['action'] = 'index';

			$layoutController  = actionControllerFactory::create('layout', 'index');
			$layoutController->dispatch(http::$request);
			application::$stack->layoutController = $layoutController;
			$layoutController->render();
		}

		if (self::$renderer == rendererFactory::JSON)
		{
			application::$stack->render();
		}

		if (self::$renderer == rendererFactory::XML)
		{
			echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
			application::$stack->render();
		}

		if (conf::i()->debug['enable'])
        {
            profiler::finish(APPLOG_ID);
        }
	}
    
    static public function dispatch($module, $action = 'index', $data = false)
    {
		$context['controller']  = actionControllerFactory::create($module, $action);
		$context['data']        = $data;

		if ($context['controller'] instanceof webActionController)
		{
			if (!application::$stack)
			{
				application::$stack = new viewStack;
			}

			if (!$data)
			{
				$context['data'] = http::$request;
			}
		}

		$context['module'] = $module;
		$context['action'] = $action;

		self::$contextIndex++;
		self::$context[self::$contextIndex] = $context;
		$code		= self::$context[self::$contextIndex]['controller']->dispatch($context['data']);
		self::$renderer	= self::$context[self::$contextIndex]['controller']->renderer;

		self::$contextIndex--;

        return $code;
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


            self::$contextIndex++;
            self::$context[self::$contextIndex] = $context;
            $code = self::$context[self::$contextIndex]['controller']->dispatch($context['data']);
            self::$contextIndex--;
        }
        catch (controllerException $e)
        {
        }
        catch (moduleException $e)
        {
        }

        return $code;
    }

    public static function registerEvent( $name, $before = false )
    {
        $eventClassName         =  $name . 'Event';
        self::$events[ $name ]   = new $eventClassName;

		if ($before)
		{
			self::$events[ $name ]->beforeDispatch = true;
		}
		else
		{
			self::$events[ $name ]->afterDispatch = true;
		}
    }


    public static function setContext( $param, $value )
    {
        self::$context[self::$contextIndex][$param] = $value;
    }

    public static function getContext( $param )
    {
        
        return self::$context[self::$contextIndex][$param];
    }

}

?>