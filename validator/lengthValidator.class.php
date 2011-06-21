<?php
class lengthValidator extends baseValidator
{
	/**
	 * Check string value for proper length
	 *
	 * @param integer $min
	 * @param integer $max
	 * @param string $message
	 */
	public function __construct($min, $max, $message)
	{
		$this->minLength = $min;
		$this->maxLength = $max;
		parent::__construct($message);
	}

	function isValid($value, $fieldname = false)
	{
		if ($this->maxLength && (mb_strlen($value) > $this->maxLength))
		{
			return false;
		}

		if ($this->minLength && (mb_strlen($value) < $this->minLength))
		{
			return false;
		}

		return true;
	}
}

