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

    static function renderLocalJs($group)
    {
        return '<script type="text/javascript" src="' . conf::i()->domains['static'] . minifier::build($group)->uri('/' . $group . '.js') . '"></script>';
    }

    static function renderRemoteJs()
    {
		$output = '';
        foreach(application::$stack->javascriptFiles as $jsFile)
		{
	        $output .= '<script type="text/javascript" src="' . $jsFile . '"></script>';
		}
		return $output;
	}

    static function renderJsSnippets()
    {
		$output = '';
		foreach(application::$stack->javascriptSnippets as $snippet)
		{
			$output .= $snippet;
		}
		return $output;
	}

}
?>
