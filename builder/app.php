<?php
        $actions    = builder::scanActions($rootdir . "/apps/" . APPLICATION);
        $classes    = builder::scanClasses($rootdir . "/apps/" . APPLICATION);

        echo APPLICATION . ': ' . count($actions) . " actions " . count($classes) . " classes \n";

        file_put_contents($rootdir . "/~cache/autoload-" . APPLICATION . ".php",
            "<?php \n\n " .
            "\$" . APPLICATION . "Classes = array( \n" . builder::arrayToCode($actions) .builder::arrayToCode($classes) . "); \n\n" .
            "\n\n ?>"
        );
?>
