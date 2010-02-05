<?php
class migrateDbController extends taskActionController
{
    public function execute($params)
    {
		$sqlDir = conf::i()->deployment->rootdir . '/sql';

        $migratedListFile   = conf::i()->rootdir . '/~data/migration/migrated.txt';

        $handle = opendir($migrationDir);
        $files  = array();
        
        while (false !== ($file = readdir($handle)))
        {
            $info = pathinfo($file);
            if ($info['extension'] != 'xml')
            {
                continue;
            }
            
            $files[] = $file;
        }
        closedir($handle);

        if (file_exists($migratedListFile))
        {
            $migratedFiles  = (array)explode("\n", @file_get_contents($migratedListFile));
        }
        else
        {
            $migratedFiles  = array();
        }

        foreach ($migratedFiles as &$migratedFile)
        {
            $migratedFile = trim($migratedFile);
        }

        foreach($files as $file)
        {
            if (!in_array($file, $migratedFiles))
            {
                $filesToMigrate[] = $file;
            }
        }

        $filesToMigrate = array_diff($files, $migratedFiles);
        
        if (!$filesToMigrate)
        {
            echo "\n\nnothing to migrate.......\n";
            return;
        }

        echo "\n\nStarting migration.......\n";
        
        $succesfullyMigrated    = array();
        $failed                 = array();
        
        foreach($filesToMigrate as $fileToMigrate)
        {
            $query  = file_get_contents($migrationDir . '/' . $fileToMigrate);
            $result = false;

			$queries = preg_split("/;+(?=([^'\"|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $query);

            try
            {
                $result = db::exec($query);
            }
            catch(Exception $e)
            {
                echo "\n********************\n";
                echo " Exception: " . $e->getMessage();
                echo "\n********************\n";
            }

            if ($result)
            {
                $succesfullyMigrated[] = $fileToMigrate;
                echo $fileToMigrate . "....... Done\n";
            }
            else
            {
                $failed[] = $fileToMigrate;
                echo $fileToMigrate . "....... Failed\n";
            }
        }

        file_put_contents($migratedListFile, "\n" . implode("\n", $succesfullyMigrated), FILE_APPEND);
        
        echo "Migrated " . count($succesfullyMigrated) .  ".......\n";
        echo "Failed " . count($failed) .  ".......\n\n";
    }
}

?>