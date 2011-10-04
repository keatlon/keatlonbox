<?php

class authMongo extends authBase
{
	function clearCredentials()
	{
		parent::clearCredentials();
	}

	function me($id)
	{
		return (_mongo::id($id) == auth::id());
	}
}
