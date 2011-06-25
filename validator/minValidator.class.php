<?php
class minValidator extends baseValidator
{
	public function __construct($count, $message)
	{
		$this->count = $count;
		parent::__construct($message);
	}
	
	function isValid($value, $fieldname = false)
	{
		if (!is_array($value))
		{
			return false;
		}

		return (count($value) >= $this->count);
	}
}

