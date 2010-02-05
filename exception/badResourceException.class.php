<?php
class badResourceException extends applicationException
{
	public function __construct()
	{
            $this->message = 'Ресурс не найден';
            parent::__construct();
	}
}
?>
