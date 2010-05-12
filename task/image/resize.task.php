<?php
class resizeImageController extends taskActionController
{
    function execute($params)
    {
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
            $options		= conf::i()->image['sizes'][$image['size']];
			if (conf::i()->image['source'])
			{
	            $storagePath	= imageStorage::cachePath($image['id'], conf::i()->image['source']);
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

            $cmd = conf::i()->image['imagick'] . ' ' . $storagePath . ' ' . $options . ' ' . $cachePath;

            if (conf::i()->image['escapecmd'])
            {
                $cmd = escapeshellcmd($cmd);
            }
            
            exec($cmd);

            if (isset(conf::i()->image['watermark'][$image['size']]))
            {
                $wmcmd = sprintf(conf::i()->image['watermark'][$image['size']], conf::i()->rootdir . '/web/images/watermark.png', $cachePath, $cachePath);

                if (conf::i()->image['escapecmd'])
                {
                    $wmcmd = escapeshellcmd($wmcmd);
                }
                exec($wmcmd);
            }

        }

        if (!file_exists($cachePath))
        {
            header("HTTP/1.1 404 Not Found");
            die();
        }

        $ifModifiedSince    = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? stripslashes($_SERVER['HTTP_IF_MODIFIED_SINCE']) : FALSE;
        $ifNoneMatch        = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? stripslashes($_SERVER['HTTP_IF_NONE_MATCH']) : FALSE;

        if ($ifModifiedSince)
        {
            header('HTTP/1.1 304 Not Modified');
            header('Content-Type: image/jpeg');
            header("Last-Modified: Tue, 12 Dec 2006 03:03:59 GMT");
			header("Cache-Control: max-age=86400");
			header("Expires: " . gmdate("D, d M Y H:i:s", date("U") - 86400 * 10) . " GMT");
            return;
        }

		if (!$params['convert'])
		{

	        $content = file_get_contents($cachePath);
			
			header('Content-Type: image/jpeg');
			header("Last-Modified: Tue, 12 Dec 2006 03:03:59 GMT");
			header("Expires: Mon, 7 Dec 2010 05:00:00 GMT");
			header("Cache-Control: max-age=86400");

			echo $content;
		}
    }
}

?>
