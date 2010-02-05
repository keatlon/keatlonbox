<?php
class staticHelper
{
    static function javascript($variable, $value, $useKeys = false)
    {
        return application::$stack->javascript($variable, $value, $useKeys);
    }

    static function addJavascriptFile($file)
    {
        return application::$stack->addJavascriptFile($file);
	}

    static function addJavascriptSnippet($snippet)
    {
        return application::$stack->addJavascriptSnippet($snippet);
    }

    static function addJavascriptOnload($snippet)
    {
        return application::$stack->addJavascriptOnload($snippet);
    }
}
?>
