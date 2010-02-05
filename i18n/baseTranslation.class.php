<?php

abstract class baseTranslation
{
    protected $namespace    = false;
    protected $locale       = false;
    
    function load($ns = 'index', $defaultLocale = 'en') {}
    function get($phrase, $locale = false, $ns = 'index') {}
}

?>
