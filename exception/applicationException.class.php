<?php

class applicationException extends Exception
{
	public function __construct( $message = '', $code = 0)
	{
		response::exception('Internal Application Error');
		log::critical($this);
	}
}
