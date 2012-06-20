<?php
abstract class form
{
	static protected $instance = false;

    protected $fields		=	array();

	protected $rules		=	array();
	protected $metaRules	=	array();

	protected $data			=	array();
	protected $meta			=	array();

	public $errors;

	function __construct($data = false, $meta = false)
	{
		$this->data	=	$data ? $data : array();
		$this->meta	=	$meta ? $meta : array();

		$this->setup();
		$this->setupMeta();
		$this->cleanup();
	}

	function setup()
	{
		
	}

	function setupMeta()
	{

	}

	function cleanup()
	{
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

		if ($this->metaRules) foreach($this->metaRules as $rule)
		{
			if (!$rule['validators'])
			{
				continue;
			}

			foreach($rule['validators'] as $validator)
			{
				if (!$validator->isValid($this->meta[$rule['field']], $rule['field']))
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

	/**
	 * Add meta fields to be validated
	 *
	 * @param string $field
	 * @param array $validators
	 */
	function addMetaRule($field, $validators = array())
	{
		$this->metaRules[]	=	array
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
