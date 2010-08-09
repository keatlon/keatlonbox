<?php
class viewStack
{
    private $stack              = array();
    private $javascript         = false;
    private $state              = array();
    
    public  $javascriptFiles    = array();
    public  $javascriptSnippets = array();
    public  $javascriptOnload   = array();

	/**
	 *
	 * @var actionController
	 */
	public  $lastController		= false;

    public function push(actionController $controller, $stackName = 'wide', $view = false, $priority = 10, $renderer = false)
    {
        if ($this->state[$stackName] == 'locked')
        {
            return false;
        }

		if (!$renderer)
		{
			$renderer = $controller->renderer;
		}

		if ($controller->moduleName != 'layout')
		{
			$this->lastController		= $controller;
		}
		
        $stackItem['controller']    = $controller;
        $stackItem['view']          = $view;
        $stackItem['renderer']      = $renderer;
        
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

    public function javascript($variable, $value, $useKeys = false)
    {
		$item['name']		= $variable;
		$item['value']		= $value;
		$item['useKeys']	= $useKeys;

        $this->javascript[] = $item;
    }

    public function renderJavascript()
    {
        if(!$this->javascript)
        {
            return;
        }

        foreach($this->javascript as $item)
        {
            echo 'var ' . $item['name'] . ' = ' . javascriptHelper::get($item['value'], $item['useKeys']) . ';';
        }
    }

    public function addJavascriptFile($file)
    {
        $this->javascriptFiles[] = $file;
    }

    public function addJavascriptSnippet($snippet)
    {
        $this->javascriptSnippets[] = $snippet;
    }

    public function addJavascriptOnload($snippet)
    {
        $this->javascriptOnload[] = $snippet;
    }
}
?>
