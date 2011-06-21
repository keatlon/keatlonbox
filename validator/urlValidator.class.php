<?php
class urlValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		
		if(filter_var($value, FILTER_VALIDATE_URL) === FALSE)
		{
			return false;
		}

		return true;
	}
}

