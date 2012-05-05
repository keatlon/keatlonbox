<?php

class authMysql extends authBase
{
    function authorize($data)
	{
        $user = userPeer::row(userPeer::cols( array('status' => 'active', 'email' => $data['email'])));

        if (!$user)
		{
            return false;
        }

        if ($user['password'] != sha1($data['password']))
		{
            return false;
        }

		$this->set($user['id'], $user['role']);

        return $user['id'];
    }
}
