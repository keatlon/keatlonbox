<?php
class cropImageController extends taskActionController
{
    function execute($params)
    {
		$sourceType			= $params[4];
		if ($sourceType == 'original')
		{
			$sourceType = false;
		}

		$destinationType	= $params[5];

		$list = imagePeer::getList();

		foreach($list as $imageId)
		{
            $coordinates            = imageStorage::getSquareCoords($imageId);
			imageStorage::crop(
				$imageId, $sourceType, $destinationType,
				$coordinates['w'],
				$coordinates['h'],
				$coordinates['l'],
				$coordinates['t']
			);
		}

    }
}

?>
