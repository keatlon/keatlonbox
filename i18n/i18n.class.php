<?php

class i18n
{
	static private $locale;
	static private $application;
	static private $phrases;
	
    static function init()
    {
        mb_internal_encoding("UTF-8");
        date_default_timezone_set('UTC');
		self::$application	=	application::$name;
		i18n::load();
	}

    static function setLocale($locale)
    {
		if (!$locale)
		{
			return false;
		}

		self::$locale	=	$locale;
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

    static function compile($application)
    {
		$phrases	=	i18n::getPhrases($application);
		
		$xml		=	simplexml_load_string(
		'<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
		<!DOCTYPE some_name [
		<!ENTITY nbsp "&#160;">
		<!ENTITY larr "&#8592;">
		<!ENTITY rarr "&#8594;">
		]>
		<i18n/>');



		if ($phrases) foreach($phrases as $phrase)
		{
			$label	=	$xml->addChild('lb');
			$label->addAttribute('name', $phrase['name']);

			if ($phrase['translations']) foreach($phrase['translations'] as $translation)
			{
				$phrase = $label->addChild('translation', $translation['phrase']);
				$phrase->addAttribute('locale', $translation['locale']);
			}
		}

		$dom = new DOMDocument();
		$dom->loadXML($xml->asXML());
		$dom->formatOutput = true;

		file_put_contents(self::getFilename($application), $dom->saveXML());
    }

    static private function getFilename($application)
    {
		return conf::i()->rootdir . '/~cache/i18n.' . $application . '.xml';
	}

    static function load($application = false, $locale = false)
    {
        self::$phrases[$application]  =	simplexml_load_file( conf::i()->rootdir . '/~cache/i18n.' . $locale . '.' . $application . '.xml');
    }

    static function get($phrase)
    {
		$locale			=	self::$locale;
		$application	=	self::$application;

		if (!self::$phrases[$application])
		{
            return $phrase;
		}

        $node = self::$phrases[$application]->xpath("/i18n/lb[@name='" . $phrase . "']/translation[@locale='" . $locale . "']");

        if (!$node)
        {
            return $phrase;
        }

        return (string)$node[0];
    }

	function addPhrase($phrase, $application)
	{
		switch(conf::i()->database['engine'])
		{
			case 'mysql':
				return mysqlTranslation::insert($phrase, $application);

			case 'mongo':
				return mongoTranslation::insert($phrase, $application);
		}
	}

	function getPhrases($application)
	{
		switch(conf::i()->database['engine'])
		{
			case 'mysql':
				return mysqlTranslation::getPhrases($application);

			case 'mongo':
				return mongoTranslation::getPhrases($application);
		}
	}
}

?>