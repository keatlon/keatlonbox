<?php

require_once conf::i()->rootdir . '/core/library/markdown/markdown.php';

class markdown
{
	static public function process($text)
	{
		return nl2br(markdown(htmlspecialchars($text)));
	}
}
?>
