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
				break;

			case	rendererFactory::XML:
				$class	=	'xmlRenderer';
				break;

			case	jsonFactory::XML:
				$class	=	'jsonRenderer';
				break;

			default:
				$class	=	'baseRenderer';
				break;
		}

        return new $class;
    }
}

