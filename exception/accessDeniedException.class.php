<?php

class accessDeniedException extends applicationException
{
	public function __construct( $message = '', $code = 0)
	{
		parent::__construct($message, $code);
		response::exception('Access Denied');
	}
}
