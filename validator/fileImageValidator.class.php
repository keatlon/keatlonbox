<?php
class fileImageValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		if (!request::file($fieldname))
        {
            return true;
        }

		return true;
	}
}
