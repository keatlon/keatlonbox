<?php
class rendererFactory
{
    const BASE      =	0;
    const HTML      =	1;
    const JSON      =	2;
    const XML		=	3;
    const DIALOG    =	4;

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

			case	rendererFactory::JSON:
				$class	=	'jsonRenderer';
				break;

			case	rendererFactory::DIALOG:
				$class	=	'dialogRenderer';
				break;

			default:
				$class	=	'baseRenderer';
				break;
		}

        return new $class;
    }
}

