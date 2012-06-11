<?php
class emailValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		
		if(filter_var($value, FILTER_VALIDATE_EMAIL) === FALSE)
		{
			return false;
		}

		return true;
	}
}

