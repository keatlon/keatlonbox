<?php
class recreateImageController extends taskActionController
{
    function execute($params)
    {
		$sourceType			= $params[4];
		if ($sourceType == 'original')
		{
			$sourceType = false;
		}

		$destinationType	= $params[5];

		$list = imagePeer::cols();

		foreach($list as $imageId)
		{
			imageStorage::convert($imageId, $sourceType, $destinationType);
			echo "image " . $imageId . " converted \n";
		}

    }
}

?>
