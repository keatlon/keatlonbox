<?php
class googleI18nController extends taskActionController
{
    function execute($params)
    {
        $items = translationPeer::getItems(translationPeer::getList(array('status' => 'pending')));
        
        foreach($items as $item)
        {
            $translation = i18n::translate('google', $item['ru_RU'], 'ru', 'uk');
            echo 'translating phrase ' . $item['id'] . "\n";
            translationPeer::update($item['id'], array('status' => 'googled', 'ua_UA' => $translation));
        }
    }
}

?>