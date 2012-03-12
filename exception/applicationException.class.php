<?php

class applicationException extends Exception
{
	public function __construct( $message = '', $code = 0)
	{
		parent::__construct($message, $code);
		log::exception($this);
	}

	public function __toString()
	{
		return  'Common Application Error';
	}
	
}
