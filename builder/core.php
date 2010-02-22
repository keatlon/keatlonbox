<?php
	$core           = builder::scanClasses($rootdir   . "/core");
	$tasks          = builder::scanTasks($rootdir     . "/core");
	$appTasks       = builder::scanTasks($rootdir     . "/lib");
	$components     = builder::scanClasses($rootdir   . "/lib");
	$actions        = builder::scanActions($rootdir   . "/core");

	echo 'core: ' . (count($core) + count($actions) + count($components) + count($tasks) + count($appTasks) ) . " classes \n";

	file_put_contents($rootdir . "/~cache/autoload-core.php",
		"<?php \n\n " .
		"\$coreClasses = array( \n" . builder::arrayToCode($core) . builder::arrayToCode($tasks) . builder::arrayToCode($appTasks) . builder::arrayToCode($components) .  builder::arraytoCode($actions) . builder::arraytoCode($configuration) ."); \n\n" .
		"\n\n ?>"
	);
?>
