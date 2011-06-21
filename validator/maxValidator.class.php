<?php
class maxValidator extends baseValidator
{
	public function __construct($count, $message)
	{
		$this->count = $count;
		parent::__construct($message);
	}

	function isValid($value, $fieldname = false)
	{
		return (count($value) <= $this->count);
	}
}

