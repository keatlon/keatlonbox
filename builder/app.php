<?php
		$application = $argv[3];

        $actions    = builder::scanActions(conf::i()->rootdir . "/apps/{$application}");
        $classes    = builder::scanClasses(conf::i()->rootdir . "/apps/{$application}");

        echo count($actions) . " {$application} actions \n";
        echo count($classes) . " {$application} classes \n";

        file_put_contents(conf::i()->rootdir . "/~cache/autoload-{$application}.php",
            "<?php \n\n " .
            "\${$application}Classes = array( \n" . builder::arrayToCode($actions) .builder::arrayToCode($classes) . "); \n\n" .
            "\n\n ?>"
        );
?>
