<?php
class scanI18nController extends taskActionController
{
    function execute($params)
    {
        $application	=	$params[4];
        $locale			=	$params[5];
		$scantime		=	time();
        $items			=	$this->scanPhrases(conf::i()->rootdir . '/apps/' . $application);

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

		if ($locale)
		{
			i18n::compile($application, $scantime);
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