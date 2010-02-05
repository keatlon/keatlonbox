<?php

class HTMLPurifier_Filter_YouTube extends HTMLPurifier_Filter
{
    
    public $name = 'YouTube';
    
    public function preFilter($html, $config, $context) {
        $pre_regex = '#<object[^>]+>.+?'.
            'http://www.youtube.com/v/([A-Za-z0-9\-_]+).+?</object>#s';
        $pre_replace = '<youtube>http://www.youtube.com/watch?v=\1</youtube>';
        return preg_replace($pre_regex, $pre_replace, $html);
    }
    
    public function postFilter($html, $config, $context) {
        $post_regex = '#<youtube>http:\/\/www.youtube.com\/watch\?v=([A-Za-z0-9\-_]+)</youtube>#';
        $post_replace = '<object width="480" height="385"><param name="movie" value="http://www.youtube.com/v/\1&hl=ru&fs=1&rel=0"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/\1&hl=ru&fs=1&rel=0" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="480" height="385"></embed></object>';
        return preg_replace($post_regex, $post_replace, $html);
    }
    
}

