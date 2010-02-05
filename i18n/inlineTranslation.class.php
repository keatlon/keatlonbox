<?php
class inlineTranslation extends baseTranslation
{
    function load($ns = 'index', $defaultLocale = 'en')
    {
        $filename = conf::i()->rootdir . '/i18n/' . $ns . '.inline.php';
        if (file_exists($filename))
        {
            $this->namespace[$ns]  = include $filename;
            $this->locale[$ns]      = $defaultLocale;
        }
    }

    function get($phrase, $locale = false, $ns = 'index')
    {
        if (!$locale)
        {
            $locale = i18n::getLocale();
        }

        if (!$this->namespace[$ns][md5($phrase)][$locale])
        {
            return $phrase;
        }

        return $this->namespace[$ns][md5($phrase)][$locale];
    }
}
?>
