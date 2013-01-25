<?php

class applicationException extends Exception
{
	public function __construct( $message = '', $code = 0)
	{
        $this->message  =   $message;
        $this->code     =   $code;

		response::exception('Internal Application Error');

        log::critical($this->getMessage(), array(
            'file'  =>  $this->getFile(),
            'line'  =>  $this->getLine(),
            'trace' =>  $this->getTraceAsString()
        ));
	}
}
