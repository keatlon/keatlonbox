<?php

class debugger
{
    static $application_time;

    public static function isDeveloper()
    {
        if (in_array($_SERVER['REMOTE_ADDR'], conf::get('developersIp'))) return true;
        return false;
    }
}
