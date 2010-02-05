<?php
class svn
{
	function getLocalRevision($path)
	{
		$cmd = 'svn info --xml ' . $path;
		$status = exec($cmd, $output);

		$metadata = simplexml_load_string(implode("\n", $output));
		$revision['revision']	= (string)$metadata->entry->commit['revision'];
		$revision['author']		= (string)$metadata->entry->commit->author;
		$revision['date']		= strtotime((string)$metadata->entry->commit->date);

		return $revision;
	}
}
?>
