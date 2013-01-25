<?php

class taskActionController extends actionController
{
    public function dispatch($data, $actionVars = false)
    {
        try
        {
            $this->beforeExecute();
            $this->execute($data);
            $this->afterExecute();
        }
        catch (Exception $e)
        {
            log::critical($e->getMessage(), array(
                'info'  =>  $e->errorInfo,
                'trace' =>  $e->getTraceAsString()
            ));

            echo "\n********************\n";
            echo "Exception: " . $e->getMessage();
            echo "\n" . $e->getTraceAsString();
            echo "\n********************\n";
       }
    }

	public function execute($data)
	{

	}
}
