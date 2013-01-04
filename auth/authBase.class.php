<?php

class authBase
{
	public		$autoCreate = false;

    function authorize($data)
	{
		return false;
	}

    function id()
	{
		return $_SESSION['KBOX'][get_class($this)]['id'];
	}

	function role()
	{
		return $_SESSION['KBOX'][get_class($this)]['role'];
	}

    function set($userId, $role = 'member')
    {
		$_SESSION['KBOX'][get_class($this)]['role']	=	$role;
        $_SESSION['KBOX'][get_class($this)]['id']	=	$userId;
    }

    function clear()
	{
		unset($_SESSION['KBOX'][get_class($this)]['role']);
		unset($_SESSION['KBOX'][get_class($this)]['id']);
    }

    function me($id)
    {
        return ($id == $this->id());
    }

}

