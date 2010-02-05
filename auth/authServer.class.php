<?php
class authServer extends authBase
{
    function authorize($data)
	{
        $user = userPeer::getItem(userPeer::getList( array('status' => 'active', 'gate' => 'normal', 'email' => $data['email'])));
		
        if (!$user)
		{
            return false;
        }

        if ($user['password'] != sha1($data['password']))
		{
            return false;
        }

		$this->setCredentials($user['id']);

        return $user['id'];
    }

	function clearCredentials()
	{
		parent::clearCredentials();
		http::redirect('/');
	}
}
?>
