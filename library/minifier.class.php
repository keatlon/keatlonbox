<?php

ini_set('include_path', conf::i()->rootdir . '/core/library/minify');

require_once dirname(__FILE__) . '/minify/Minify.php';
require_once dirname(__FILE__) . '/minify/Minify/Build.php';
require_once dirname(__FILE__) . '/minify/Minify/Logger.php';

Minify_Logger::setLogger(new log);

class minifier
{
    static public function serve($controller, $options)
    {
        Minify::setCache(conf::i()->rootdir . '/~cache/');
        Minify::serve($controller, $options);
    }

    static public function build($controller)
    {
        $staticConfig   =   (require conf::i()->rootdir . '/configuration/staticConfig.php');
        return new Minify_Build($staticConfig[$controller]);
    }

}

?>
