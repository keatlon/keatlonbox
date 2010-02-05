<?php
class requiredValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		return !empty($value);
	}
}
?>
