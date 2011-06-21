<?php

class emailActionController extends actionController
{
    const HANDLER_SENDMAIL      =	'sendmail';
    const HANDLER_PRINT         =	'print';
    const HANDLER_IGNORE        =	'ignore';

    public		$handler        =	emailActionController::HANDLER_SENDMAIL;
    protected	$subject		=	'';
    protected	$name			=	'';
    protected	$email			=	'';

    function __construct($moduleName, $actionName)
    {
        $this->moduleName   = $moduleName;
        $this->actionName   = $actionName;
    }

    public function dispatch($data)
    {
        try
        {
            $handler = $this->compose($data);
			if ($handler)
			{
				$this->handler = $handler;
			}

			$this->process();
        }
        catch (dbException $e)
        {
			log::exception($e);
            $this->response['code'] = self::EXCEPTION;
            $this->response['errors']       = $e->getMessage();
        }
        catch (Exception $e)
        {
            $this->response['code'] = self::EXCEPTION;
            $this->response['errors']       = $e->getMessage();
            log::exception($e);
       }

       return $this->response['code'];
    }

    public function process()
    {
		if ($this->handler == self::HANDLER_IGNORE)
		{
			return true;
		}

		$v = new stringValidator(validatorRules::EMAIL, '');
		if (!$this->email || !$v->isValid($this->email))
		{
			return false;
		}

        $renderer = rendererFactory::create('email');
        $result = $renderer->render($this);

        if (!$result)
        {
            $this->response['code'] = emailActionController::ERROR;
        }
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function setSubject($value)
    {
        $this->subject = $value;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

	function beforeRender()
	{
		ob_start();
	}

	function afterRender()
	{
		$content	= ob_get_contents();
		ob_end_clean();

        if ($this->action_vars)
        {
            foreach($this->action_vars as $var_name => $var_value)
            {
				$params[$var_name] = $var_value;
            }
        }

		$content	=	textHelper::smartParse($content, $params);

		if (!conf::i()->email['enabled'])
		{
			return;
		}

        if ($this->handler == emailActionController::HANDLER_SENDMAIL)
        {
            return email::send($this->name, $this->email, $this->subject, $content);
        }
	}

}