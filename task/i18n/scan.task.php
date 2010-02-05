<?php
class scanI18nController extends taskActionController
{
    function execute($params)
    {
        $application = $params[4];

        $items = $this->scanPhrases(conf::i()->rootdir . '/apps/' . $application);

        $phrases    = translationPeer::getItems(translationPeer::getList());
        $hashes     = tools::extractKey('hash', $phrases);


        foreach($items as $item)
        {
            foreach($item['phrases'] as $phrase)
            {
                $translation['hash']            =   md5($phrase);
                $translation['application']     =   $application;

                $index = array_search($translation['hash'], $hashes);
                
                if (!$index)
                {
                    $translation['status']  =   'pending';
                    $translation['ru_RU']      =   $phrase;
                    translationPeer::insert($translation);
                    echo 'phrase id ' . $phrases[$index]['id'] . ' created' . "\n";
                }
                else
                {
                    if (!$updated[$phrases[$index]['id']])
                    {
                        translationPeer::update($phrases[$index]['id'], array('created' => time()));
                        echo 'phrase id ' . $phrases[$index]['id'] . ' updated' . "\n";
                        $updated[$phrases[$index]['id']] = true;
                    }

                }


            }
        }
    }

    function scanPhrases($path)
    {
        $files = builder::_readdir($path, '|(.*)\.php$|');

        foreach($files as $file)
        {
            $content = file_get_contents($file);
            preg_match_all("#__\(\'(.*)\'\)#msuU", $content, $matches);
            $item['filename']   = $file;
            $item['phrases']    = $matches[1];
            $items[] = $item;
        }

        return $items;
    }
}

?>