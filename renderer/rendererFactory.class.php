<?php
class rendererFactory
{
    const BASE      =	'base';
    const HTML      =	'text/html';
    const JSON      =	'application/json';
    const XML		=	'application/xml';

    /**
     * @return baseRenderer
     */
    static public function create($type)
    {
		switch($type)
		{
			case	rendererFactory::HTML :
				$class	=	'htmlRenderer';

			case	rendererFactory::XML:
				$class	=	'xmlRenderer';

			case	jsonFactory::XML:
				$class	=	'jsonRenderer';

			default:
				$class	=	'baseRenderer';
		}

        return new $class;
    }
}

