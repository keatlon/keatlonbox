<?php
class doDeploymentController extends taskActionController
{
    public function execute($params)
    {
		deploymentPlan::load();
		deploymentPlan::prepare($params[1], $params[2], $params[3]);
		$this->stats = deploymentQueue::run();

		dd($this->stats);
    }
}

?>