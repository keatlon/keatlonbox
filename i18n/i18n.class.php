<?php

class i18n
{
	static private $instance;
	
    static function init()
    {
        mb_internal_encoding("UTF-8");
        date_default_timezone_set('UTC');

        if (conf::i()->application[application::$name]['i18n']['ns'])
        foreach(conf::i()->application[application::$name]['i18n']['ns'] as $ns)
        {
            i18n::i()->load($ns);
        }
	}

	static function i()
    {
		if (!i18n::$instance)
        {
	        $translationClass = conf::i()->i18n['engine'] . 'Translation';
			i18n::$instance = new $translationClass;
		}

		return i18n::$instance;
	}

    static function setLocale($locale)
    {
		if (!$locale)
		{
			return false;
		}

		$this->locale	=	$locale;
		
		setlocale(LC_ALL, $locale);
		cookie::set('locale', $locale);
    }

    static function getLocale($short = false)
    {
        if (!$_COOKIE['locale'])
        {
            $defaultLocale = conf::i()->application[application::$name]['i18n']['defaultLocale'];
            i18n::setLocale($defaultLocale);
            return $defaultLocale;
        }
        
        return $_COOKIE['locale'];
    }

    static function translate($engine, $phrase, $in, $out)
    {
        $url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=" . urlencode($phrase) . "&langpair=" . $in . "%7C" . $out;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $body = curl_exec($ch);
        curl_close($ch);

        return json_decode($body, true);
    }

    function import()
    {
    }

    function export()
    {
    }

    function compile($application, $locale = 'en')
    {
    }

    function load($application, $locale = 'en')
    {
        $this->phrases[$application]  =	simplexml_load_file( conf::i()->rootdir . '/~cache/i18n.' . $locale . '.' . $application . '.xml');
    }

    function get($phrase, $locale = false, $application = false)
    {
        if (!$locale)
        {
            $locale = $this->locale;
        }

        if (!$application)
        {
            $application = $this->application;
        }

        $node = $this->phrases[$ns]->xpath("/i18n/lb[@name='" . $phrase . "']/translation[@locale='" . $locale . "']");

        if (!$node)
        {
            return $phrase;
        }

        return (string)$node[0];
    }

	function addPhrase($translation)
	{
		switch(conf::i()->db['engine'])
		{
			case 'mysql':
				mysqlTranslation::insert($phrase);
				break;

			case 'mongo':
				mongoTranslation::insert($phrase);
				break;
		}
	}
}

?>