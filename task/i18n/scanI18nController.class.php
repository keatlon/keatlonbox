<?php
class scanI18nController extends taskActionController
{
    function execute($params)
    {
        $application	=	$params[4];
        $locale			=	$params[5];

		i18n::scan($application, $locale);

		if ($locale)
		{
			i18n::compile($application, $scantime);
		}
    }

}

?>