<?php

class applicationException extends Exception
{
	public function __construct( $message = false )
	{
			$this->message = $message;
			log::exception($this);
	}

	public function __toString()
	{
            return  'Common Application Error';
	}
	
}
