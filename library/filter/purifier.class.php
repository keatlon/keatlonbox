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
            self::$config->set('HTML', 'DefinitionID', 'enduser-customize.html tutorial');
            self::$config->set('HTML', 'DefinitionRev', 1);
            self::$config->set('Cache', 'DefinitionImpl', null); // remove this later!
            self::$config->set('HTML', 'AllowedElements', 'a,em,b,strong,i,img,youtube,h1,h2,h3,ul,li,ol,quote,cut');
            self::$config->set('HTML', 'AllowedAttributes', 'a.href,a.title,img.src,img.alt,img.align,img.class');
            self::$config->set('Filter', 'YouTube', true);
            self::$config->set('Cache', 'SerializerPath', conf::i()->rootdir . '/~cache');

            $def = self::$config->getHTMLDefinition(true);
            $def->addElement( 'youtube', 'Inline', 'Inline', array(), array() );
            $def->addElement( 'quote', 'Block', 'Flow', array(), array() );
            $def->addElement( 'cut', 'Inline', 'Empty', array(), array() );
            
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


