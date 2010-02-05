<?php
class xmlTranslation extends baseTranslation
{
    function load($ns = 'index', $defaultLocale = 'en')
    {
        $this->namespace[$ns]   = simplexml_load_file( conf::i()->rootdir . '/i18n/' . $ns . '.xml');
        $this->locale[$ns]      = $defaultLocale;
    }
    
    function get($phrase, $locale = false, $ns = 'index')
    {
        if (!$locale)
        {
            $locale = $this->locale[$ns];
        }

        $node = $this->namespace[$ns]->xpath("/i18n/lb[@name='" . $phrase . "']/translation[@locale='" . $locale . "']");

        if (!$node)
        {
            return $phrase;
        }

        return (string)$node[0];
    }
}
?>
