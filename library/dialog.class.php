<?php

class dialog
{
	static function close()
	{
		jquery::raw('dialog.close()');
	}

	static function options($options)
	{
		response::set('options', $options);
	}

	static function position($position)
	{
		response::set('position', $position);
	}


}