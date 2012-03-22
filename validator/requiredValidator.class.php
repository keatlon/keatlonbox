<?php
class requiredValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		if (!is_array($value))
		{
			$value	=	trim($value);
		}

		return		!empty($value);
	}
}

