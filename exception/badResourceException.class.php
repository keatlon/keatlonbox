<?php

class badResourceException extends applicationException
{
	public function __construct()
	{
		parent::__construct('Ресурс не найден');
	}
}
