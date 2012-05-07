<?php

class applicationException extends Exception
{
	public function __construct( $message = '', $code = 0)
	{
		parent::__construct($message, $code);
		response::exception('Internal Application Error');
		log::exception($this);
	}
}
