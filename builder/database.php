<?php


	foreach(conf::i()->database['pool'] as $dbName => $dbConnection)
	{
		$dbName     =   conf::i()->database['pool']['master']['dbname'];
		$tables     =   db::rows('SHOW TABLES FROM ' . $dbName);
		$modelPath  =   conf::i()->rootdir . '/lib/model';

		foreach($tables as $table)
		{
			$tableName = $table['Tables_in_' . $dbName];
			$parts = explode('_', $tableName);

			if (count($parts) > 1)
			{
				for($l = 1; $l < count($parts);$l++)
				{
					$parts[$l] = ucfirst($parts[$l]);
				}
			}

			$className = implode('', $parts) . 'Peer';
			$classPath = $modelPath . '/' . $className . '.class.php';

			// GENERATE BASE CLASS
			$primaryKey = 'id';
			$fields     = array();

			$columns = db::rows('show columns from ' . $tableName);

			foreach($columns as $column)
			{
				if ($column['Key'] == 'PRI')
				{
					$primaryKey = $column['Field'];
				}
			}

			$baseClassName = implode('', $parts) . 'BasePeer';
			$baseClassPath = $modelPath . '/base/' . $baseClassName . '.class.php';
			$xml = simplexml_load_file(conf::i()->rootdir . '/core/database/basePeerClass.xml');
			$baseClassContent = str_replace('%BASECLASSNAME%', $baseClassName, $xml->body);
			$baseClassContent = str_replace('%CLASSNAME%', $className, $baseClassContent);
			$baseClassContent = str_replace('%TABLENAME%', $tableName, $baseClassContent);
			$baseClassContent = str_replace('%PRIMARYKEY%', $primaryKey, $baseClassContent);
			$baseClassContent = str_replace('%FIELDS%', implode(',', $fields), $baseClassContent);
			file_put_contents($baseClassPath, $baseClassContent);

			$xml = simplexml_load_file(conf::i()->rootdir . '/core/database/peerClass.xml');
			$classContent = str_replace('%BASECLASSNAME%', $baseClassName, $xml->body);
			$classContent = str_replace('%CLASSNAME%', $className, $classContent);
			if (!file_exists($classPath))
			{
				file_put_contents($classPath, $classContent);
			}
		}

		echo count($tables) . ' tables found';
	}

?>
