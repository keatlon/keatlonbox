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
        catch ( PDOException $e)
        {
			log::critical($e->getMessage(), array(
                'info'  =>  $e->errorInfo,
                'trace' =>  $e->getTraceAsString()
            ), 'mysql');

            echo "\n********************\n";
            echo " DB Exception: " . $e->getMessage();
            echo "\n" . $e->getTraceAsString()
            echo "\n********************\n";
        }
        catch (Exception $e)
        {
            echo "\n********************\n";
            echo " Exception: " . $e->getMessage();
            echo "\n" . $e->getTraceAsString()
            echo "\n********************\n";

            log::critical($e->getMessage(), array(
                'info'  =>  $e->errorInfo,
                'trace' =>  $e->getTraceAsString()
            ));
       }
    }

	public function execute($data)
	{

	}
}
