<?php
class captchaValidator extends baseValidator
{
	function isValid($value, $fieldname = false)
	{
        captcha::init();

        if (!captcha::validate($value))
        {
            // $this->message = captcha::$response->error;
            return false;
        }

        return true;
	}
}

?>
