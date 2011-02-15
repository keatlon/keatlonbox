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
		if (trim($translation) == '')
		{
			return false;
		}

		switch(conf::i()->database['engine'])
		{
			case 'mysql':
				return mysqlTranslation::translatePhrase($hash, $lang, $translation);

			case 'mongo':
				return mongoTranslation::translatePhrase($hash, $lang, $translation);
		}
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
		$path = conf::i()->i18n['path'] ? conf::i()->i18n['path'] : '/~cache';
		return conf::i()->rootdir . $path . '/i18n.' . $application . '.xml';
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

		$hashedPhrase = $phrase;

		if (conf::i()->translation['type'] == 'hash')
		{
			$hashedPhrase = md5($phrase);
		}

		$node = self::$phrases[self::$application]->xpath("/i18n/lb[@name='" . $hashedPhrase . "']/translation[@locale='" . self::$locale . "']");

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

	function removePhrase($hash)
	{
		switch(conf::i()->database['engine'])
		{
			case 'mysql':
				return mysqlTranslation::removePhrase($hash);

			case 'mongo':
				return mongoTranslation::removePhrase($hash);
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

	function getUntranslatedPhrases($application, $lang)
	{
		switch(conf::i()->database['engine'])
		{
			case 'mysql':
				return mysqlTranslation::getUntranslatedPhrases($application, $lang);

			case 'mongo':
				return mongoTranslation::getUntranslatedPhrases($application, $lang);
		}
	}

	function getLostPhrases($application)
	{
		switch(conf::i()->database['engine'])
		{
			case 'mysql':
				return mysqlTranslation::getLostPhrases($application);

			case 'mongo':
				return mongoTranslation::getLostPhrases($application);
		}
	}

    static function scan($application, $locale)
    {
		$scantime		=	time();
        $items			=	i18n::scanPhrases(conf::i()->rootdir . '/apps/' . $application);

        foreach($items as $item)
        {
            foreach($item['phrases'] as $original)
            {
                $phrase['scantime']		=	$scantime;
                $phrase['application']	=	$application;
                $phrase['locale']		=	$locale;
                $phrase['filename']		=	$item['filename'];
                $phrase['original']		=	$original;
                $phrase['hash']			=	md5($original);

				i18n::addPhrase($phrase, $application);
            }
        }

		switch(conf::i()->database['engine'])
		{
			case 'mysql':
				mysqlTranslation::setLostPhrases($application, $scantime);

			case 'mongo':
				mongoTranslation::setLostPhrases($application, $scantime);
		}

		return count($items);
	}

    static function scanPhrases($path)
    {
        $files = builder::_readdir($path, '|(.*)\.php$|');

        foreach($files as $file)
        {
            $content = file_get_contents($file);
            preg_match_all("#__\(['\"]{1}(.*)['\"]{1}\)#msuU", $content, $matches);
            $item['filename']   = $file;
            $item['phrases']    = $matches[1];
            $items[] = $item;
        }

        return $items;
    }

	static function export($application)
	{
		header('Content-Type: application/xml');
		header('Content-Disposition: attachment; filename="i18n.' . $application . '.xml"');
		header("Expires: Mon, 7 Dec 2010 05:00:00 GMT");
		header("Cache-Control: max-age=86400");
		echo file_get_contents(self::getFilename($application));
	}
}

?>