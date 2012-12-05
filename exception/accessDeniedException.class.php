<?php

class accessDeniedException extends applicationException
{
	public function __construct( $message = '', $code = 0)
	{
		response::exception('Access Denied');
	}
}
