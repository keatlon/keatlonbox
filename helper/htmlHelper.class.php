<?php

class htmlHelper
{
    static function selected($current, $value)
    {
        return (bool)($current == $value) ? 'selected' : '';
    }
}