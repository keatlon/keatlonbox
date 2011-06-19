<?php
class authMongo extends authBase
{
    function authorize($data)
	{
        $user = users::i()->findOne(array('status' => 'active', 'email' => $data['email'], 'password' => sha1($data['password'])));

        if (!$user)
		{
            return false;
        }

		$this->setCredentials((string)$user['_id']);
		$this->setExtraCredentials(array
		(
			'email'			=>	$user['email']
		));

        return (string)$user['_id'];
    }

	function clearCredentials()
	{
		parent::clearCredentials();
		response::redirect('/');
	}

	function me($id)
	{
		return (_mongo::id($id) == auth::id());
	}
}
?>
