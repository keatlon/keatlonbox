<?php

class controllerException extends applicationException
{
	public function __construct( $message = '', $code = 0)
	{
		response::exception('Bad Controller');
	}

}
