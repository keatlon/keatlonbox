<?php
class deploymentTaskFactory
{
	static function create($task, $environment, $plan, $release)
	{
		$className = (string)$task['name'] . 'DeploymentTask';
		
		if (!class_exists($className))
		{
			return false;
		}

		return new $className($task, $environment, $plan, $release);
	}
}
?>
