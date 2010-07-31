<?php
	include dirname(__FILE__) . "/../conf/init.php";

	$items = translationPeer::getItems(translationPeer::getList());

	foreach($items as $item)
	{
		$translations[]	=	"
			'" . md5($item['ru_RU']) . "' => array
			(
				'ru_RU'    => '" . addslashes($item['ru_RU']) . "',
				'ua_UA'    => '" . addslashes($item['ua_UA']) . "'
			)";
	}


	$content	=	"<?php

return array (" . implode(',', $translations) . ");

?>";

    file_put_contents(dirname(__FILE__) . "/../../i18n/index.inline.php", $content);

?>
