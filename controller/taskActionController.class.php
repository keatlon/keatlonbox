<?php

class taskActionController extends actionController
{
    public function dispatch($data, $actionVars = false)
    {
        $this->beforeExecute();
        $this->execute($data);
        $this->afterExecute();
    }

	public function execute($data)
	{

	}
}