<?php
class buildI18nController extends taskActionController
{
    function execute($params)
    {
        $ns = $params[4];

        $items = translationPeer::getItems(translationPeer::getList());
        foreach($items as $item)
        {
            $translationData .= "'" . $item['hash'] . "' => array (\n\r\t'ru_RU'    => '" . str_replace("'", "\'", $item['ru_RU']) . "',\n\t'ua_UA'    => '" . str_replace("'", "\'", $item['ua_UA']). "'),\n\n";
        }

        $fh = fopen(conf::i()->rootdir . "/i18n/" . $ns . ".inline.php", "w+");
        fwrite($fh, "<?php \n\n return array (\n " . $translationData . ");\n\n ?>");
        fclose($fh);
    }
}

?>