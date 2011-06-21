<?php

class controllerException extends applicationException
{
    public function __construct()
    {
        $this->message = 'controller not found';
        parent::__construct();
    }
}
