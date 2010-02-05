<?php
class fileRequiredValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		if (!http::$files[$fieldname])
		{
			return false;
		}

		if (http::$files[$fieldname]['error'] !== 0)
		{
			return false;
		}

		return true;
	}
}
?>