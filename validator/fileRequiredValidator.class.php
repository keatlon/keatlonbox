<?php
class fileRequiredValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
		return !request::file($fieldname) ? true : false;
	}
}
