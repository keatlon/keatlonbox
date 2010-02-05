<?php

define('APPLICATION', 'task');
define('ENVIRONMENT', $argv[1]);

include dirname(__FILE__) . "/../configuration/base/applicationConfig.class.php";

application :: execute($_SERVER['argv'][2], $_SERVER['argv'][3]);


?>