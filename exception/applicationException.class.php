<?php

class applicationException extends Exception
{
	public function __construct( $message = '', $code = 0)
	{
		log::exception($this);
		parent::__construct($message, $code);
	}

	public function __toString()
	{
		return  'Common Application Error';
	}
	
}
