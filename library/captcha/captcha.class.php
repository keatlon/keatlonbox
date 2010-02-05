<?php

require_once dirname(__FILE__) . '/recaptcha/recaptchalib.php';

class captcha
{
    protected static    $publicKey      = false;
    protected static    $privateKey     = false;
    public static       $response       = false;

    static public function init()
    {
        self::$publicKey    = conf::i()->captcha['public_key'];
        self::$privateKey   = conf::i()->captcha['private_key'];
    }

    static public function generate()
    {
        return recaptcha_get_html(self::$publicKey);
    }

    static public function validate($response)
    {
        self::$response = recaptcha_check_answer (
            self::$privateKey,
            $_SERVER["REMOTE_ADDR"],
            $_REQUEST["recaptcha_challenge_field"],
            $response
        );


        if (!self::$response->is_valid)
        {
            return false;
        }

        return true;
    }

}

?>
