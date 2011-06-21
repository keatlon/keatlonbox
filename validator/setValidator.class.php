<?php
class setValidator extends baseValidator
{

	public function __construct($set, $message)
	{
		$this->set = $set;
		parent::__construct($message);
	}

	function isValid($value, $fieldname = false)
	{
        if (!is_array($value))
        {
            return in_array($value, $this->set);
        }

		foreach ($value as $option)
		{
			if (!in_array($option, $this->set))
			{
				return false;
			}
		}

		return true;
	}
}

