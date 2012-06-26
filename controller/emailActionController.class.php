<?php

class emailActionController extends webActionController
{
    protected	$__subject		=	'';
    protected	$__name			=	'';
    protected	$__email		=	'';
	protected	$__format		=	render::XML;
	protected	$__stream		=	render::STREAM_SMTP;

    public function dispatch($data)
    {
		$this->compose($data);

		$v = new emailValidator('');

		if (!$this->__email || !$v->isValid($this->__email))
		{
			return response::exception('Bad e-mail [' . $this->__email . ']');
		}

		return render::controller($this);
    }

    public function setName($value)
    {
        $this->__name = $value;
    }

    function setSubject($value)
    {
        $this->__subject = $value;
    }

    public function setEmail($value)
    {
        $this->__email = $value;
    }
}