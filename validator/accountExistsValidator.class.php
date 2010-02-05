<?php
class accountExistsValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		if (!$value)
		{
			return true;
		}

        if (userPeer::getList(array( 'email' => $value)))
		{
			return true;
		}

		return false;
	}
}
?>
