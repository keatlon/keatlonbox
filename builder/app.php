<?php
        $actions    = builder::scanActions(conf::i()->rootdir . "/apps/" . APPLICATION);
        $classes    = builder::scanClasses(conf::i()->rootdir . "/apps/" . APPLICATION);

        echo APPLICATION . ': ' . count($actions) . " actions " . count($classes) . " classes \n";

        file_put_contents(conf::i()->rootdir . "/~cache/autoload-" . APPLICATION . ".php",
            "<?php \n\n " .
            "\$" . APPLICATION . "Classes = array( \n" . builder::arrayToCode($actions) .builder::arrayToCode($classes) . "); \n\n" .
            "\n\n ?>"
        );
?>
