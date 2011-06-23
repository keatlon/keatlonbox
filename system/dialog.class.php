<?php

class dialog
{
	static function close()
	{
		js::raw('dialog.close()');
	}
}