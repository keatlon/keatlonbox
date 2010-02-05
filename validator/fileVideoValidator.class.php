<?php
class fileVideoValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		if (!http::$files[$fieldname]['name'])
        {
            return true;
        }

		return true;
	}
}
?>