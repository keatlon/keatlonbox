<?php
class deploymentTaskFactory
{
	static function create($task, $space, $plan, $release)
	{
		$className = (string)$task['name'] . 'DeploymentTask';
		
		if (!class_exists($className))
		{
			return false;
		}

		return new $className($task, $space, $plan, $release);
	}
}
