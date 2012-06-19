<?php

include dirname(__FILE__) . "/conf/init.php";

application :: execute($_SERVER['argv'][1], $_SERVER['argv'][2]);

