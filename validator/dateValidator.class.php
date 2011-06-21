<?php
class dateValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
        if (!$value)
        {
            return true;
        }

        $stamp = strtotime( $value );
        $month = date( 'm', $stamp );
        $day   = date( 'd', $stamp );
        $year  = date( 'Y', $stamp );

        return checkdate( $month, $day, $year );
	}
}
