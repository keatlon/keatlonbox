<?php
	$core           = builder::scanClasses(conf::i()->rootdir   . "/core");
	$configuration  = builder::scanClasses(conf::i()->rootdir   . "/configuration");
	$tasks          = builder::scanTasks(conf::i()->rootdir     . "/core");
	$appTasks       = builder::scanTasks(conf::i()->rootdir     . "/lib");
	$components     = builder::scanClasses(conf::i()->rootdir   . "/lib");
	$actions        = builder::scanActions(conf::i()->rootdir   . "/core");

	echo 'core: ' . (count($core) + count($actions) + count($components) + count($configuration) + count($tasks) + count($appTasks) ) . " classes \n";

	file_put_contents(conf::i()->rootdir . "/~cache/autoload-core.php",
		"<?php \n\n " .
		"\$coreClasses = array( \n" . builder::arrayToCode($core) . builder::arrayToCode($tasks) . builder::arrayToCode($appTasks) . builder::arrayToCode($components) .  builder::arraytoCode($actions) . builder::arraytoCode($configuration) ."); \n\n" .
		"\n\n ?>"
	);
?>
