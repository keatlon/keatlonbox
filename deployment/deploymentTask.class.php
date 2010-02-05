<?php
abstract class deploymentTask
{
	protected	$taskDocument;
	protected	$environment;
	protected	$plan;
	protected	$release;
	public		$task;

	function  __construct($xmlTask, $environment, $plan, $release)
	{
		$this->taskDocument	= clone $xmlTask;
		
		$this->environment	= $environment;
		$this->plan			= $plan;
		$this->release		= $release;

		$this->task['status']		=	'new';
		$this->task['environment']	=	$this->environment;
		$this->task['plan']			=	$this->plan;
		$this->task['release']		=	$this->release;
		$this->task['name']			=	(string)$this->taskDocument['name'];
		$this->task['once']			=	(int)(bool)$this->taskDocument['once'];
		$this->task['description']	=	(string)$this->taskDocument['description'];
		$this->task['ignore_error']	=	(int)(bool)$this->taskDocument['ignore_error'];
	}

	function prepare()
	{
		
	}

	function run()
	{

	}

	function getHash()
	{
		return $this->task['hash'];
	}

}
?>
