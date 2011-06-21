<?php
class partialHelper
{
    public static function render($templateRnD1983001, $variablesRnD1983001 = array(), $returnRnD1983001 = false)
    {
        if ($variablesRnD1983001) foreach ($variablesRnD1983001 as $nameRnD1983001 => $valueRnD1983001)
        {
            $$nameRnD1983001 = $valueRnD1983001;
        }

        if ($returnRnD1983001)
        {
            ob_start();
        }

        include baseRenderer::getTemplatePath($templateRnD1983001);

        if ($returnRnD1983001)
        {
            $templateRnD1983001 = ob_get_contents();
			ob_end_clean();

            return $templateRnD1983001;
        }
    }
}
