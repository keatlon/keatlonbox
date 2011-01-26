<?php
class mongoTranslation extends baseTranslation
{
    static function insert($phrase)
	{
		$row = translations::i()->findOne(array('hash' => md5($phrase['original'])));

		if ($row)
		{
			return;
		}

		translations::i()->insert($phrase);
	}

	static function getPhrases($application)
	{
		$cursor = translations::i()->find(array('application' => $application));

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
?>
