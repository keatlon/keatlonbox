<?php
class requiredValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		$value	=	trim($value);
		return		!empty($value);
	}
}

