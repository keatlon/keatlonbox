<?php
class accountPasswordValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
        if (userPeer::getList(array( 'email' => $value, 'status' => 'active')))
		{
			return false;
		}

		return true;
	}
}
?>
