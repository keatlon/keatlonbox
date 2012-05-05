<?php
class authFacebook extends authBase
{
    function authorize($data)
	{
		$fbUserId		= fb::id();

		if (!$fbUserId)
		{
			return false;
		}

		$fbUser = userPeer::row(userPeer::cols(array('facebook_id' => $fbUserId)));

		if (!$fbUser)
		{
			return false;
		}

		$this->set($fbUser['id'], $fbUser['role']);

		auth::gateway('facebook');

        return $fbUser['id'];
    }
}
