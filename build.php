<?php

define('PRODUCT',	$argv[1]);
define('ENVIRONMENT', $argv[2]);
define('BUILD_TARGET', $argv[3]);

include dirname(__FILE__) . "/conf/init.php";
include dirname(__FILE__) . "/system/builder.class.php";
include dirname(__FILE__) . "/builder/" . BUILD_TARGET . ".php";

?>