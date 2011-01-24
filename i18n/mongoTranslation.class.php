<?php
class mongoTranslation extends baseTranslation
{
    static function insert($phrase)
	{
		$row = translations::i()->findOne(array('hash' => md5($phrase['original'])));

		if ($row)
		{
			translations::i()->update(_mongo::primary($row['_id']), array($));
		}

		translations::i()->insert($phrase);
	}
    static function update($id, $phrase, $timestamp) {}
    static function remove($id) {}

}
?>
