<?php

ini_set('include_path', conf::i()->rootdir . '/core/library/minify');

require_once dirname(__FILE__) . '/minify/Minify.php';
require_once dirname(__FILE__) . '/minify/Minify/Build.php';
require_once dirname(__FILE__) . '/minify/Minify/Logger.php';

Minify_Logger::setLogger(new log);

class minifier
{
    static function serve($controller, $options)
    {
        Minify::setCache(conf::i()->rootdir . '/~cache/');
        Minify::serve($controller, $options);
    }

    static function build($controller)
    {
        $staticConfig   =   (require self::getConfig());
        return new Minify_Build($staticConfig[$controller]);
    }

	static function init()
	{
		minifier::serve('MinApp', array
		(
			'minApp' => array('groups' => (require self::getConfig())),
			'maxAge' => 31536000
		));
	}

	static function getConfig()
	{
		return CONFDIR . '/' . PRODUCT . '.static.php';
	}

}

?>
