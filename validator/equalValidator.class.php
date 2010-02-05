<?php
class equalValidator extends baseValidator
{
	public function __construct($origin, $message)
	{
		$this->origin = $origin;
		parent::__construct($message);
	}

	function isValid($value, $fieldname = false)
	{
		return $value == $this->origin;
	}
}

?>
