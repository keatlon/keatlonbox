<?php
class rendererFactory
{
    const BASE      = 'base';
    const HTML      = 'html';
    const JSON      = 'json';
    const XML		= 'xml';

    /**
     * @return baseRenderer
     */
    static public function create($type)
    {
        $className = $type . 'Renderer';
        return new $className;
    }
}
?>
