<?php
class accountSigninValidator extends baseValidator
{
	/**
	 * Check string to match given regular expression
	 *
	 * @param string $pattern
	 * @param string $message
	 */
	function  __construct($params, $message)
	{
		$this->params = $params;
		parent::__construct($message);
	}

	function isValid($value, $fieldname = false)
	{
        $userId = auth::i('server')->authorize(array('email' => $this->params['email'], 'password' => $this->params['password']));

        if ($userId)
        {
			return true;
		}

		return false;
	}
}
?>
