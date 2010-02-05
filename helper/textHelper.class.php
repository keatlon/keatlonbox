<?php

class textHelper
{
	static function escape($value, $options = array())
	{
	    return htmlspecialchars($value, ENT_QUOTES);
	}

	static function numeric($text, $num)
	{
		$start_pos  = strpos($text, '(:');

        while($start_pos)
		{
            $end_pos    = strpos($text, ')', $start_pos);

            $variants   = substr($text, $start_pos + 2, $end_pos - $start_pos - 2);

            $set = explode(',', $variants);

            $replacement = $set[self::numericForm($num)];

            if ($num === 0)
			{
                $replacement = $set[3];
                $num = $set[0];
            }

            $text = str_replace('(:' . $variants . ')', $replacement, $text);
    		$start_pos  = strpos($text, '(:');
        }

	    return htmlspecialchars(sprintf($text, $num), ENT_QUOTES);
	}


    /**
     * @param integer $number
     * @return integer
     */
	static private function numericForm($number)
	{
        // process exception
        if ($number > 5 && $number < 21)
		{
            return 3;
        }

        $diff = $number % 10;

        if ($diff == 0)
		{
            return 0;
        }

        if ($diff == 1)
		{
            return 1;
        }

        if ($diff > 1 && $diff < 5)
		{
            return 2;
        }

        return 3;
	}

	static function pasrseLinks($text)
	{
        
	}

    static public function completeLink($url)
	{
        if (!trim($url))
		{
            return false;
        }

        if (strpos($url, 'http://') === false)
		{
            $url = 'http://' . $url;
        }

        return $url;
    }


    public static function smartCut($text)
	{
        $briefLength = mb_strpos($text, "<cut />");

        if (!$briefLength)
		{
            $briefLength = mb_strlen($text);
        }

        if ($briefLength)
		{
            return mb_substr($text, 0, $briefLength);
        }

        return $text;
    }

    public static function smartParse($phrase, $params = array())
	{
        if (!$params)
		{
            return $phrase;
        }

        foreach ($params as $placeholder => $value)
		{
            $phrase = str_replace('%' . $placeholder . '%', $value, $phrase);
        }

        $replacement = ($params['gender'] == 'male') ? "\$1" : "\$2";
        $phrase = preg_replace('|\(g:(.*),(.*)\)|', $replacement, $phrase);

        return $phrase;
    }

    static function getCapacity($text)
	{
        return mb_strlen(strip_tags($text));
    }

    static function getCost($capacity, $pricePerThousand)
	{
        return round(($capacity / 1000) * $pricePerThousand, 2);
    }

}

?>