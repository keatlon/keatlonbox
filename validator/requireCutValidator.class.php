<?php
class requireCutValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
        if (strpos($value, '<cut />') !== false)
        {
            return true;
        }

        if (strpos($value, '<cut/>') !== false)
        {
            return true;
        }

        return false;
	}
}

