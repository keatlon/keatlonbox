<?php
class javascriptHelper
{
	public static function get($value, $useKeys = false)
	{

		if ( is_numeric($value) )
		{
			return $value;
		}

		if ( is_bool($value) )
		{
			return ( $value ? 'true' : 'false' );
		}
        
		if ( is_string($value) )
		{
			if ( strpos($value, 'eval:') === 0 )
			{
				return substr($value, 5);
			}

			$value = str_replace(array('\\', "'"), array('\\\\', '\\\''), $value);
			$value = str_replace(array("\n", "\r"), array('\' + "\\n" + \'', ''), $value);

			return "'" . $value . "'";
		}

		if ( is_array($value) )
		{
			$output = self::getArray($value, $useKeys);
			return $output;
		}
        
        return 'null';
	}

	protected static function getArray( $array, $useKeys = false )
	{
		foreach ( $array as $k => $val )
		{
			if ( is_array($val) )
			{
				$array[$k] = self::getArray( $val, $useKeys );
			}
			else
			{
				$k		= addslashes($k);

				if ($useKeys)
				{
					$array[$k] = "'{$k}':" . self::get($val);
				}
				else
				{
					$array[$k] = self::get($val);
				}

			}
		}

		return ' {' .implode(', ', $array) . '}';
	}
}

?>
