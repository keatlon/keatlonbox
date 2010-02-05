<?php
class emailActionController extends actionController
{
    const HANDLER_SENDMAIL      = 'sendmail';
    const HANDLER_PRINT         = 'print';
    const HANDLER_IGNORE        = 'ignore';

    public		$handler        = emailActionController::HANDLER_SENDMAIL;
    protected	$subject      = '';
    protected	$recipient    = '';
    protected	$email        = '';

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

    public function setRecipient($value)
    {
        $this->recipient = $value;
    }

    public function setSubject($value)
    {
        $this->subject = $value;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function setRecipientByUserId($userId)
    {
        $user       = userPeer::getItem($userId);
        $userData   = userDataPeer::getItem($userId);

        $this->setEmail($user['email']);
        $this->setRecipient($userData['name']);
    }
}
?>