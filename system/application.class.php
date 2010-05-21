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
	static public		$layout		= 'index';

	static public function run()
	{
        application::$name	= APPLICATION;

        if (conf::i()->debug['enable'])
        {
			define('TS_APPLICATION_RUN', microtime(true));
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

        if (conf::i()->debug['enable'])
        {
			define('TS_APPLICATION_RENDER', microtime(true));
        }

		if (self::$renderer == rendererFactory::HTML)
		{
			$layout = self::getLayout();

			$context['data']['module'] = 'layout';
			$context['data']['action'] = $layout;

			$layoutController  = actionControllerFactory::create('layout', $layout);
			$layoutController->dispatch(http::$request);
			application::$stack->layoutController = $layoutController;

			if (application::getContext('layout'))
			{
				$layoutController->setView(application::getContext('layout'));
			}
			
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
			profiler::finish(profiler::start(profiler::SYSTEM, 'app::render',	false, TS_APPLICATION_RENDER));
			profiler::finish(profiler::start(profiler::SYSTEM, 'app::run',		false, TS_APPLICATION_RUN));
			profiler::finish(profiler::start(profiler::SYSTEM, 'app::global',	false, TS_APPLICATION_GLOBAL));
			profiler::finish(profiler::start(profiler::SYSTEM, 'sys::global',	false, $_SERVER['REQUEST_TIME']));
			$logItems = profiler::get();

			if (self::$renderer == rendererFactory::HTML)
			{
				include conf::i()->rootdir . '/core/web/layout/view/debug.view.php';
			}
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
				comet::init();
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

    public static function setLayout( $layout )
    {
        self::$layout = $layout;
    }

    public static function getLayout()
    {
        return self::$layout;
    }
}

?>