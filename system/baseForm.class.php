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

	function setup()
	{
		
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
		if ($this->rules) foreach($this->rules as $rule)
		{
			if (!$rule['validators'])
			{
				continue;
			}

			foreach($rule['validators'] as $validator)
			{
				if (!$validator->isValid($this->data[$rule['field']], $rule['field']))
				{
					$this->errors[$rule['field']] = $validator->getErrorMessage();
					break;
				}
			}
		}

		return (bool)!$this->errors;
	}

	/**
	 * Add fields to be validated
	 *
	 * @param string $field
	 * @param array $validators
	 */
	function addRule($field, $validators = array())
	{
		$this->rules[]	=	array
		(
			'field'			=>	$field,
			'validators'	=>	$validators
		);
	}

	function getErrors()
	{
		return $this->errors;
	}
}
