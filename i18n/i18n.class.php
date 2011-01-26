<?php

class i18n
{
	static private $locale		=	false;
	static private $application	=	false;
	static private $phrases		=	array();
	
    static function init()
    {
        mb_internal_encoding("UTF-8");
        date_default_timezone_set('UTC');

		self::$application	=	application::$name;
		self::$locale		=	self::getLocale();
		
		i18n::load(self::$application);
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

    static function translatePhrase($hash, $lang, $translation)
    {
		switch(conf::i()->database['engine'])
		{
			case 'mysql':
				return mysqlTranslation::translatePhrase($hash, $lang, $translation);

			case 'mongo':
				return mongoTranslation::translatePhrase($hash, $lang, $translation);
		}
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

    static function load($application = false)
    {
        self::$phrases[$application]  =	simplexml_load_file(self::getFilename($application));
    }

    static function get($phrase)
    {
		if (!self::$phrases[self::$application])
		{
            return $phrase;
		}

        $node = self::$phrases[self::$application]->xpath("/i18n/lb[@name='" . $phrase . "']/translation[@locale='" . self::$locale . "']");

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

	function getPhrases($application = false)
	{
		if (!$application)
		{
			$application = application::$name;
		}

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