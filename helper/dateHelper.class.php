<?php
class dateHelper
{
	static function monthr($id = false)
    {
         $monthr = array(
            1 =>
            __('января'),
            __('февраля'),
            __('марта'),
            __('апреля'),
            __('мая'),
            __('июня'),
            __('июля'),
            __('августа'),
            __('сентября'),
            __('октября'),
            __('ноября'),
            __('декабря')
        );

        if ($id)
        {
            return $monthr[(int)$id];
        }

        return $monthr;
    }

	static function month($id = false)
    {
         $month = array(
            1 =>
            __('январь'),
            __('февраль'),
            __('март'),
            __('апрель'),
            __('май'),
            __('июнь'),
            __('июль'),
            __('август'),
            __('сентябрь'),
            __('октябрь'),
            __('ноябрь'),
            __('декабрь')
        );

        if ($id)
        {
            return $month[(int)$id];
        }

        return $month;
    }

	static function day($timestamp)
	{
		return date($timestamp, 'd');
	}

	static function date($timestamp = false, $format = false)
	{
		if (!$timestamp)
		{
			$timestamp = time();
		}

		if (!$format)
		{
			$format = 'd-m-Y';
		}

		return self::formatTime($timestamp);
	}

	static function formatTime($timestamp)
	{
		return strftime('%B %d, %Y', $timestamp);
	}

	static function time($timestamp = false, $format = false)
	{
		if (!$timestamp)
		{
			$timestamp = time();
		}
		
		if (!$format)
		{
			$format = 'H:i';
		}

		return date($format, $timestamp);
	}

	static function human($timestamp, $year = true)
	{
        if ($year)
        {
            $year = ' ' . date('Y', $timestamp);
        }
        return  date('j', $timestamp) . ' ' . self::monthr(date('n', $timestamp)) . $year ;
	}

	static function dayAge($timestamp)
	{
        return floor( (time() - $timestamp) / 86400);
	}

}
?>
