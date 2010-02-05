<?php
class accountActiveValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		if (!$value)
		{
			return true;
		}

        if (userPeer::getList(array( 'email' => $value, 'status' => 'active')))
		{
			return true;
		}

		return false;
	}
}
?>
