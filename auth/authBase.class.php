<?php

abstract class authBase
{
	public		$autoCreate = false;

    function authorize($data)
	{
		return false;
	}

    function getCredentials() 
	{
		return $_SESSION['c'][get_class($this)];
	}

    function createUser($data) {}

    function setCredentials($userId, $role = 'member')
    {
		$this->setExtraCredentials(array('role' => $role));
        $_SESSION['c'][get_class($this)]    = $userId;
    }

    function setExtraCredentials($data)
    {
        $_SESSION['ce'][get_class($this)]    = $data;
    }

    function getExtraCredentials()
    {
        return $_SESSION['ce'][get_class($this)];
    }


    function hasCredentials()
	{
        return isset($_SESSION['c'][get_class($this)]);
    }

    function clearCredentials()
	{
        unset($_SESSION['c'][get_class($this)]);
        unset($_SESSION['ce'][get_class($this)]);
		session::destroy();
    }

    function me($id)
    {
        return ($id == $this->getCredentials());
    }

}

