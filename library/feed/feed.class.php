<?
class feed
{
	function init()
	{
		require_once conf::i()->rootdir . '/core/library/feed/FeedWriter.php';
		require_once conf::i()->rootdir . '/core/library/feed/FeedItem.php';
	}
}
?>