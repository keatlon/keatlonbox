<?php
class tools
{
    static function filter($array, $keys)
    {
        $keys = explode(',', $keys);

        $result = array();
        foreach($array as $key => $value)
        {
            if (in_array($key, $keys))
            {
                $result[$key] = $value;
            }
        }
        
        return $result;
    }

	static public function makeAssociative($key, $array)
    {
		$result	=	array();
		foreach($array as $item)
		{
			$result[(string)$item[$key]] = $item;
		}

		return $result;
	}

	static public function extend($field, $mainList, $mainKey, $sourceList, $sourceKey)
    {
		$sourceList = tools::makeAssociative($sourceKey, $sourceList);

		foreach($mainList as &$mainItem)
		{
			$mainItem[$field] = $sourceList[$mainItem[$mainKey]];
		}

		return $mainList;
	}

    static public function extractKey($key, $array)
    {
        $result = array();

        if (!is_array($array))
        {
            return $result;
        }

        foreach($array as $item)
        {
            $result[] = $item[$key];
        }
        return $result;
    }

    static public function shift($array)
    {
        if (!$array)
        {
            return false;
        }
        
        return array_shift($array);
    }


}
?>
