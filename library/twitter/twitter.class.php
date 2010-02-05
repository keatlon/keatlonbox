<?php
class twitter
{
	static function init()
	{
		require_once conf::i()->rootdir . '/core/library/twitter/twitteroauth.php';
		require_once conf::i()->rootdir . '/core/library/oauth/OAuth.php';
	}
}
?>
