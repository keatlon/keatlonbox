<?php
class fileVideoValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		return !request::file($fieldname) ? true : false;
	}
}
