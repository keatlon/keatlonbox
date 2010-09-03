<?php
class emailRenderer extends baseRenderer
{
    public function render(actionController $__action, $__view = false)
    {
        $rnd1983view = '/frontend/notification/view/' . $__action->actionName;

        $content    =   __(partialHelper::render($rnd1983view, false, true));
        $footer     =   __(partialHelper::render("/frontend/notification/view/footer", false, true));

        $content    = str_replace('%recipient%', $__action->recipient, $content);
        if ($__action->action_vars)
        {
            foreach($__action->action_vars as $var_name => $var_value)
            {
                $content	= str_replace('%' . $var_name . '%', $var_value, $content);
                $footer		= str_replace('%' . $var_name . '%', $var_value, $footer);
            }
        }

        if ($__action->handler == emailActionController::HANDLER_SENDMAIL)
        {
            return email::send($__action->recipient, $__action->email, $__action->subject, $content . "\n\n" . $footer);
        }

        return true;
    }
}
?>