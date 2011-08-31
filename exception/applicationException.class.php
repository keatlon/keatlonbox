<?php

class applicationException extends Exception
{
	public function __construct( $message, $code = 0, Exception $previous = null )
	{
		log::exception($this);
		parent::__construct($message, $code, $previous);
	}

	public function __toString()
	{
            return  'Common Application Error';
	}
	
}
