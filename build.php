<?php

define('PRODUCT',	$argv[1]);
define('ENVIRONMENT', $argv[2]);
define('BUILD_TARGET', $argv[3]);

include dirname(__FILE__) . "/../core/conf/applicationConfig.class.php";
include dirname(__FILE__) . "/../core/system/builder.class.php";
include dirname(__FILE__) . "/../core/builder/" . BUILD_TARGET . ".php";

?>