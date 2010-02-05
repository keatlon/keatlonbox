<?php
class accessDeniedException extends applicationException
{
	public function __construct()
	{
            $this->message = 'Доступ запрещен';
            parent::__construct();
	}
}
?>
