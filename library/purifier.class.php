<?
require_once conf::i()->rootdir . '/core/library/purifier/HTMLPurifier.includes.php';

class purifier
{
    static protected $purifier = false;
    static protected $config = false;

    static public function purify($text)
    {
        if (!self::$purifier)
        {
            self::$config = HTMLPurifier_Config::createDefault();

			self::$config->set('AutoFormat.Linkify', true);

			self::$config->set('HTML', 'DefinitionID', 'enduser-customize.html tutorial');
            self::$config->set('HTML', 'DefinitionRev', 1);
            // self::$config->set('Cache', 'DefinitionImpl', null); // remove this later!
            self::$config->set('HTML', 'AllowedElements', 'a,em,b,strong,i,img,youtube,h2,ul,li,ol,quote,cut,br');
            self::$config->set('HTML', 'AllowedAttributes', 'a.href,a.title,img.src,img.alt,img.align,img.class');
            self::$config->set('Cache', 'SerializerPath', conf::i()->rootdir . '/~cache');

            self::$purifier = new HTMLPurifier(self::$config);
        }

        return self::$purifier->purify($text);
    }

    static public function unpurify($text)
    {
        $filter = new HTMLPurifier_Filter_YouTube();
        return $filter->preFilter($text, $config, $context);
    }

}


