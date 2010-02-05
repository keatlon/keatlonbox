<?php
abstract class baseValidator
{
	protected $message;

	function __construct($message)
	{
		$this->message = $message;
	}
	
	public function isValid($value, $fieldname = false)
	{
		return true;
	}
	
	public function getErrorMessage()
	{
		return $this->message;
	}
}
?>