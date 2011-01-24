<?php
class scanI18nController extends taskActionController
{
    function execute($params)
    {
        $application = $params[4];

        $items		=	$this->scanPhrases(conf::i()->rootdir . '/apps/' . $application);
		$scantime	=	time();

        foreach($items as $item)
        {
            foreach($item['phrases'] as $original)
            {
                $phrase['scantime']		=	$item['filename'];
                $phrase['filename']		=	$item['filename'];
                $phrase['original']		=	$original;
                $phrase['hash']			=	md5($phrase);
                $phrase['application']	=	$application;

				i18n::addPhrase($translation);
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