<?php
class accountNotExistsValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		if (!$value)
		{
			return true;
		}

        if (userPeer::getList(array( 'email' => $value)))
		{
			return false;
		}

		return true;
	}
}
?>
