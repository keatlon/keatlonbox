<?php
class viewStack
{
    private $stack              = array();
    private $javascript         = false;
    private $state              = array();
    
    public  $javascriptFiles    = array();
    public  $javascriptSnippets = array();
    public  $javascriptOnload   = array();

    public function push(actionController $controller, $stackName = 'wide', $view = false, $priority = 10, $renderer = false)
    {
        if ($this->state[$stackName] == 'locked')
        {
            return false;
        }

		if ($renderer)
		{
			$controller->renderer	=	$renderer;
		}

        $stackItem['controller']    = $controller;
        $stackItem['view']          = $view;
        
        $this->stack[$stackName][$priority][] = $stackItem;
        $this->state[$stackName] = 'opened';
    }

    public function pop($stackName = 'wide')
    {
        return array_shift($this->stack[$stackName]);
    }

    public function render($stackName = 'wide')
    {
        if (!$this->stack[$stackName])
        {
            return false;
        }

        ksort($this->stack[$stackName]);

        foreach ($this->stack[$stackName] as $priority)
        {
            foreach ($priority as $stackItem)
            {
				$this->currentController = $stackItem['controller'];
                $stackItem['controller']->renderer = $stackItem['renderer'];
                $stackItem['controller']->render($stackItem['view']);
            }
        }
    }

    public function clear($stackName = 'wide')
    {
        $this->stack[$stackName] = false;
    }

    public function lock($stackName = 'wide')
    {
        $this->state[$stackName] = 'locked';
    }

}

