<?php

class application
{
    static protected    $context        = array();
    static protected    $contextIndex   = 0;
    
    static public       $name       =	null;
    static public       $events     =	null;
	static public		$options	=	false;
	
	/**
	 * @var viewStack
	 */
    static public       $stack      = null;
    static public       $i18n       = null;
    static public       $renderer   = null;
	static public		$layout		= 'index';

	const	EVENT_BEFORE_CONTROLLER		= 0;
	const	EVENT_BEFORE_LAYOUT			= 1;
	const	EVENT_BEFORE_RENDER			= 2;

	static public function init()
	{
        application::$name	= APPLICATION;

		session::init();
        log::init();
        mc::init();
		i18n::init();

		if (!conf::i()->application[APPLICATION]['auth'])
		{
			conf::i()->application[APPLICATION]['auth'] = array('server');
		}

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
	}

	static public function run()
	{
		ob_start();

		application::init();

        if (conf::i()->debug['enable'])
        {
			define('TS_APPLICATION_RUN', microtime(true));
        }

        try
        {
			if (application::$events) foreach(application::$events as $event)
			{
				if ($event->position == application::EVENT_BEFORE_CONTROLLER)
				{
					$event->handle();
				}
			}

			application::dispatch(http::$request['module'], http::$request['action']);







			



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


		if (application::$events) foreach(application::$events as $event)
		{
			if ($event->position == application::EVENT_BEFORE_LAYOUT)
			{
				$event->handle();
			}
		}


		if (self::$renderer == rendererFactory::HTML)
		{
			comet::init();

			$layout = self::getLayout();

			$context['data']['module'] = 'layout';
			$context['data']['action'] = $layout;

			$layoutController  = actionControllerFactory::create('layout', $layout);
			$layoutController->dispatch(http::$request);
			application::$stack->layoutController = $layoutController;

			if (application::$events) foreach(application::$events as $event)
			{
				if ($event->position == application::EVENT_BEFORE_RENDER)
				{
					$event->handle();
				}
			}

			if (application::getContext('layout'))
			{
				$layoutController->setView(application::getContext('layout'));
			}

			staticHelper::javascript('app', array(
				'context'	=> array
				(
					'module'	=>	application::$context[count(application::$context)]['module'],
					'action'	=>	application::$context[count(application::$context)]['action'],
				),

				'options'	=>	application::$options
			));

			$layoutController->render();
		}

		if (self::$renderer == rendererFactory::JSON)
		{

			if (application::$events) foreach(application::$events as $event)
			{
				if ($event->position == application::EVENT_BEFORE_RENDER)
				{
					$event->handle();
				}
			}

			application::$stack->render();
		}

		if (self::$renderer == rendererFactory::XML)
		{
			if (application::$events) foreach(application::$events as $event)
			{
				if ($event->position == application::EVENT_BEFORE_RENDER)
				{
					$event->handle();
				}
			}

			echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
			application::$stack->render();
		}

		if (conf::i()->debug['enable'])
        {
			profiler::finish(profiler::start(profiler::SYSTEM, 'app::render',	false, TS_APPLICATION_RENDER));
			profiler::finish(profiler::start(profiler::SYSTEM, 'app::run',		false, TS_APPLICATION_RUN));
			profiler::finish(profiler::start(profiler::SYSTEM, 'app::global',	false, TS_APPLICATION_GLOBAL));
			profiler::finish(profiler::start(profiler::SYSTEM, 'sys::global',	false, $_SERVER['REQUEST_TIME']));
			
			$logItems = profiler::get(profiler::SYSTEM);
			$sqlItems = profiler::get(profiler::SQL);
			
			// profiler::firephp()->info(count($sqlItems));
			// profiler::firephp()->group('System');

			foreach($logItems as $logItem)
			{
				// profiler::firephp()->info($logItem);
			}

			// profiler::firephp()->groupEnd();


			if (self::$renderer == rendererFactory::HTML)
			{
				// include conf::i()->rootdir . '/core/web/layout/view/debug.view.php';
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
		$code			= self::$context[self::$contextIndex]['controller']->dispatch($context['data']);
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

    public static function registerEvent( $name, $position = application::EVENT_BEFORE_CONTROLLER )
    {
        $eventClassName			=	$name . 'Event';
        self::$events[ $name ]	=	new $eventClassName;
		self::$events[ $name ]->position = $position;
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

	static function getLastAction()
	{
		return self::$stack->lastController;
	}
}

?>