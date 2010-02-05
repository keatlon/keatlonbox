<?php
class timeValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
        if (!$value)
        {
            return true;
        }
        
        list($hours, $minutes) = explode(':', $value);
        return $this->checktime( $hours, $minutes );
	}

    function checktime($hour, $minute)
    {
        if ($hour > -1 && $hour < 24 && $minute > -1 && $minute < 60)
        {
            return true;
        }
    }
}
?>
