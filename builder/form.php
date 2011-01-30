<?php
	include dirname(__FILE__) . "/../conf/init.php";

	$formPath	=   conf::i()->rootdir . '/lib/form';
	$views		=	builder::scanViews($rootdir . "/apps/" . APPLICATION);
	$template	=	simplexml_load_file(conf::i()->rootdir . '/core/builder/form.xml');

	foreach ($views as $filename)
	{
		$content	=	file_get_contents($filename);
		$res = preg_match_all('|<form.*action=[\'"]{1}(.*)[\'"]{1}.*>|U', $content, $matches);

		if (!$matches[1]) 
		{
			continue;
		}

		foreach($matches[1] as $action)
		{
			$classname		=	implode('', array_map('ucfirst', explode('/', $action))) . 'BaseForm';
			$classname{0}	=	strtolower($classname{0});
			$classFilename	=	$formPath . '/' . $classname . '.class.php';

			if (file_exists($classFilename))
			{
				continue;
			}

			file_put_contents($classFilename, str_replace('%BASECLASSNAME%', $classname, $template->body));
		}
	}


?>
