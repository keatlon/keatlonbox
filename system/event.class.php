<?php
class event
{
	protected   $period			= 0;
	public      $data			= false;
	public      $beforeDispatch = false;
	public      $afterDispatch	= false;

	function handle()
	{
		
	}

	function get()
	{
		return $this->data;
	}
}
?>
