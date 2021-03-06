<?php
class resizeImageController extends taskActionController
{
    function execute($params)
    {
		$staticFile	= realpath(conf::$conf['image']['cache'] . '/' . $params['req']);
		
		if( file_exists($staticFile))
		{
			if (!$params['convert'])
			{
				$this->out($staticFile);
			}
			return;
		}

        $info   = pathinfo($params['req']);

        list($image['id'], $image['size'], $image['salt'], $image['extra']) = explode('_', $info['filename']) ;

		if ($image['extra'])
		{
			$info['filename'] = substr($info['filename'], 0, strpos($info['filename'], '_' . $image['extra']));
		}

        $cachePath = imageStorage::cachePath($image['id'], $image['size']);
        $checkinfo = pathinfo($cachePath);

        if ($info['filename'] != $checkinfo['filename'])
        {
            header("HTTP/1.1 404 Not Found");
            die();
        }
        
        if (!file_exists($cachePath))
        {
            $options		= conf::$conf['image']['sizes'][$image['size']];
			
			if (conf::$conf['image']['source'] && !in_array($image['size'], (array)conf::$conf['image']['ignoresource']))
			{
	            $storagePath	= imageStorage::cachePath($image['id'], conf::$conf['image']['source']);
				if (!file_exists($storagePath))
				{
		            $storagePath	= imageStorage::storagePath($image['id']);
					if (!file_exists($storagePath))
					{
						header("HTTP/1.1 404 Not Found");
						die();
					}
				}
			}
			else
			{
	            $storagePath	= imageStorage::storagePath($image['id']);
			}

            storage::preparePath($cachePath);

            $cmd = conf::$conf['image']['imagick'] . ' ' . $storagePath . ' ' . $options . ' ' . $cachePath;

            if (conf::$conf['image']['escapecmd'])
            {
                $cmd = escapeshellcmd($cmd);
            }
            
            exec($cmd);
        }

        if (!file_exists($cachePath))
        {
            header("HTTP/1.1 404 Not Found");
            die();
        }

		if (!$params['convert'])
		{
			$this->out($cachePath);
		}
    }

	function out($filename)
	{
		$content = file_get_contents($filename);

		header('Content-Type: image/jpeg');
		header("Last-Modified: Tue, 12 Dec 2006 03:03:59 GMT");
		header("Expires: Mon, 7 Dec 2010 05:00:00 GMT");
		header("Cache-Control: max-age=86400");

		echo $content;
	}
}

?>
