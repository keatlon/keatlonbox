<?php
abstract class baseForm
{
    protected $allowedFields = array();
	protected $fields;
	protected $step;
	public $errors;
	
	function __construct($data, $step = false)
	{
		$this->data = $data;
		$this->step = $step;
	}
	
	function validate()
	{
		$validated = true;
		
		if ($this->fields) foreach($this->fields as $field => $params)
		{
			if (!$params['validators'])
			{
				continue;
			}

			if ($this->step && ($this->step != $params['step']))
			{
				continue;
			}

			foreach($params['validators'] as $validator)
			{
				if (!$validator->isValid($this->data[$field], $field))
				{
					$this->errors[$field] = $validator->getErrorMessage();
					$validated = false;
					break;
				}
			}
		}

		return $validated;
	}

	/**
	 * Add fields to be validated
	 *
	 * @param string $name
	 * @param array $validators
	 * @param integer $step
	 */
	function addField($name, $validators = array(), $step = false)
	{
		$this->fields[$name]['validators'] = $validators;
		$this->fields[$name]['step'] = $step;
	}
}
?>
