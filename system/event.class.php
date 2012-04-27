<?php
class event
{
	const	EVENT_BEFORE_CONTROLLER		=	0;
	const	EVENT_BEFORE_ACL 			=	1;
	const	EVENT_BEFORE_RENDER			=	2;

	static protected	$events			=	array();

	/**
	 * @static
	 * @param $name
	 * @param int $position
	 */
    public static function register( $name, $position = application::EVENT_BEFORE_CONTROLLER )
    {
        $eventClassName			=	$name . 'Event';
        self::$events[ $name ]	=	new $eventClassName;
		self::$events[ $name ]->position = $position;
    }

	/**
	 * @static
	 * @param $type
	 */
	static public function process($type)
	{
		if (self::$events) foreach(self::$events as $event)
		{
			if ($event->position == $type)
			{
				$event->handle(request::get());
			}
		}
	}
}
