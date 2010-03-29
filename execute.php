<?php

define('ENVIRONMENT', $argv[1]);
include dirname(__FILE__) . "/conf/init.php";

application :: execute($_SERVER['argv'][2], $_SERVER['argv'][3]);


?>