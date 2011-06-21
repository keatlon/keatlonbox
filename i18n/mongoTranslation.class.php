<?php
class mongoTranslation extends baseTranslation
{
    static function insert($phrase)
	{
		$row = translations::i()->findOne(array('hash' => md5($phrase['original'])));

		if ($row)
		{
			return translations::i()->update(_mongo::primary($row['_id']), array('$set' => array('scantime' => $phrase['scantime'])));
		}

		translations::i()->insert($phrase);
	}

	static function removePhrase($hash)
	{
		translations::i()->remove(array('hash' => $hash));
	}
	
	static function fetchPhrases($cursor)
	{
		while($cursor->hasNext())
		{
			$row			=	$cursor->getNext();
			$phrase			=	array();
			$translations	=	array();

			if ($row['locale'])
			{
				$phrase['name']	=	$row['hash'];
				$phrase['hash']	=	$row['hash'];

				$translations[$row['locale']]	=	array
				(
					'locale'	=>	$row['locale'],
					'phrase'	=>	$row['original']
				);
			}
			else
			{
				$phrase['name']	=	$row['original'];
				$phrase['hash']	=	md5($row['original']);
			}

			if ($row['translations']) foreach($row['translations'] as $translation)
			{
				$translations[$translation['locale']]	=	array
				(
					'locale'	=>	$translation['locale'],
					'phrase'	=>	$translation['phrase']
				);
			}

			$phrase['translations']	=	$translations;
			$phrases[]	=	$phrase;
		}

		return $phrases;
	}

	static function getUntranslatedPhrases($application, $lang)
	{
		return self::fetchPhrases(translations::i()->find(array(
			'application'			=> $application,
			'translations.locale'	=>	array('$ne' => $lang)
		)));
	}

	static function getLostPhrases($application)
	{
		return self::fetchPhrases(translations::i()->find(array('application' => $application, 'lost' => 1)));
	}

	static function setLostPhrases($application, $scantime)
	{
		return translations::i()->update(
			array('scantime'	=> array('$ne' => $scantime)),
			array('$set'		=>	array('lost' =>	1)),
			array('multiple'	=> true)
		);
	}

	static function getPhrases($application)
	{
		return self::fetchPhrases(translations::i()->find(array('application' => $application)));
	}

	static function translatePhrase($hash, $locale, $phrase)
	{
		$translation['locale']	=	$locale;
		$translation['phrase']	=	$phrase;

		translations::i()->update(array('hash' => $hash), array
		(
			'$pull'	=>	array('translations' => array('locale' => $locale)),
		));
		
		translations::i()->update(array('hash' => $hash), array
		(
			'$push'	=>	array('translations' => $translation)
		));
	}
		
    static function update($id, $phrase, $timestamp) {}
    static function remove($id) {}

}
