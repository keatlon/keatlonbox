<?php
class stringValidator extends baseValidator
{
	/**
	 * Check string to match given regular expression
	 *
	 * @param string $pattern
	 * @param string $message
	 */
	function  __construct($pattern, $message)
	{
		$this->pattern = $pattern;
		parent::__construct($message);
	}

	function isValid($value, $fieldname = false)
	{
        if (!trim($value))
        {
            return true;
        }

		if (preg_match($this->pattern, $value))
		{
			return true;
		}

		return false;
	}
}
