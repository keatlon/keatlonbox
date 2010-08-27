<?php
abstract class baseForm
{
	static protected $instance = false;

    protected $fields		= array();
	protected $rules;
	protected $data;
	
	public $errors;

	function __construct($data = false)
	{
		if ($data)
		{
			$this->data	=	$data;
		}

		$this->setup();
		$this->cleanup();
	}

	function cleanup()
	{
		if (!$fields)
		{
			return true;
		}

		foreach($this->data as $k => $v)
		{
			if (!in_array($k, $this->fields))
			{
				unset($this->data[$k]);
			}
		}
	}

	function get($field = false)
	{
		if (!$field)
		{
			return $this->data;
		}
		
		return $this->data[$field];
	}

	function validate()
	{
		if ($this->rules) foreach($this->rules as $field => $validators)
		{
			if (!$validators)
			{
				continue;
			}

			foreach($validators as $validator)
			{
				if (!$validator->isValid($this->data[$field], $field))
				{
					$this->errors[$field] = $validator->getErrorMessage();
					break;
				}
			}
		}

		return (bool)!$this->errors;
	}

	/**
	 * Add fields to be validated
	 *
	 * @param string $name
	 * @param array $validators
	 * @param integer $step
	 */
	function addRule($name, $validators = array())
	{
		$this->rules[$name] = $validators;
	}

	function getErrors()
	{
		return $this->errors;
	}
}
?>
