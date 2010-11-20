<?php
class partialHelper
{
    public static function render($template, $variables = array(), $return = false)
    {
        if ($variables)
        foreach ($variables as $name => $value)
        {
            $$name = $value;
        }

        if ($return)
        {
            ob_start();
        }

        include baseRenderer::getTemplatePath($template);

        if ($return)
        {
            $template = ob_get_contents();
			ob_end_clean();

            return $template;
        }
    }
}
?>
