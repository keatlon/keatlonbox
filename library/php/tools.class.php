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
		foreach($array as $item)
		{
			$result[$item[$key]] = $item;
		}

		return $result;
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
