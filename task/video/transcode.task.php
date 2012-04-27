<?php
class transcodeVideoController extends taskActionController
{
	function execute($params)
	{
        if (lock::active('video'))
        {
            return;
        }

        lock::add('video');
        
		$list = videoPeer::getList(array('status' => 'pending'), array(), false, 10);
		
		if (!$list)
		{
            lock::remove('video');
			return false;
		}

		foreach($list as $id)
		{
			$item = videoPeer::getItem($id);

			if ($item['status'] != 'pending')
			{
				continue;
			}

			videoPeer::update($id, array('status' => 'transcoding'));

			$input  = videoStorage::storagePath($id);
			$output = videoStorage::cachePath($id);
            
            videoStorage::preparePath($output);
			echo "\n" . conf::$conf['video']['encoder'] . ' -i ' . $input . ' ' . conf::$conf['video']['sizes']['normal'] . ' ' . $output;

			exec(conf::$conf['video']['encoder'] . ' -i ' . $input . ' ' . conf::$conf['video']['sizes']['normal'] . ' ' . $output);
			videoPeer::update($id, array('status' => 'processed', 'processed' => time()));
			echo $inputFilename ." processed \n";
		}

        lock::remove('video');
	}
}

?>