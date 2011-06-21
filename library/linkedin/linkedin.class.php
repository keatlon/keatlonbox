<?php
class linkedin
{
	static function init()
	{
		require_once conf::i()->rootdir . '/core/library/linkedin/linkedinoauth.php';
		require_once conf::i()->rootdir . '/core/library/oauth/OAuth.php';
	}
}

