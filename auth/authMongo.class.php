<?php

class authMongo extends authBase
{
	function me($id)
	{
		return (_mongo::id($id) == auth::id());
	}
}
