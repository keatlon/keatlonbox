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

				$translations[]	=	array
				(
					'locale'	=>	$row['locale'],
					'phrase'	=>	$row['original']
				);
			}
			else
			{
				$phrase['name']	=	$row['original'];
			}

			if ($row['translations']) foreach($row['translations'] as $translation)
			{
				$translations[]	=	array
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

    static function update($id, $phrase, $timestamp) {}
    static function remove($id) {}

}
?>
