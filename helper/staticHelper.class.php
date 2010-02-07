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

    static function renderContext()
    {
		return "application.addContext({ signedIn:" . (int)auth::hasCredentials() . ", module: '" . http::$request['module'] . "', action: '" . http::$request['action'] . "' });";
	}

    static function renderSlicerJs()
    {
		$output = 'var slicers = {';
		if ($slicers = slicer::iterate())
		{
			foreach($slicers as $name => $slicer)
			{
				$generatedSlicers[] = "'" . $name . "' : {name: '" . $name . "', mode: '" . $slicer->mode . "', page: " . (int)$slicer->page . ", maxPage : " . (int)$slicer->maxPage . ", enableKeys: " . (int)$slicer->enableKeys . ",obj: null}";
			}
			$output .= implode(',', $generatedSlicers);
		}
		$output .= '};';

		return $output;
	}

	static function renderOnloadJs()
	{
		$output = '';
		if (application::$stack->javascriptOnload)
		foreach(application::$stack->javascriptOnload as $onload)
		{
			$output .= $onload;
		}
		return $output;
	}
}
?>
