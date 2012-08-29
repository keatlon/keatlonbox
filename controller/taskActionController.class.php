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
        catch (dbException $e)
        {
			log::exception($e);
            echo "\n********************\n";
            echo " DB Exception: " . $e->getMessage();
            echo "\n********************\n";
        }
        catch (Exception $e)
        {
            echo "\n********************\n";
            echo " Exception: " . $e->getMessage();
            echo "\n********************\n";
            log::exception($e);
       }
    }

	public function execute($data)
	{

	}
}
