<?php

define('ENVIRONMENT', $argv[1]);
define('BUILD_TARGET', $argv[2]);

include dirname(__FILE__) . "/../configuration/base/applicationConfig.class.php";
include dirname(__FILE__) . "/../core/system/builder.class.php";
include dirname(__FILE__) . "/../core/builder/" . BUILD_TARGET . ".php";

?>