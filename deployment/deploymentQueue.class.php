<?php
class deploymentQueue
{
	static $queue				= array();
	static $plan				= false;
	static $space				= false;
	static $release				= false;
	static $completed			= false;

	static function init($space, $plan, $release)
	{
		self::$plan			= $plan;
		self::$space	= $space;
		self::$release		= $release;
		
		$queueFilename = deploymentPlan::parse('${system::plan}') . '/' . self::$release .'/' . self::$space . '-' . self::$plan . '.queue';


		if (!file_exists($queueFilename))
		{
			$xml = simplexml_load_string('<?xml version="1.0"?><queue><plan></plan><done></done></queue>');
			file_put_contents($queueFilename, $xml->asXML());
		}

		self::$completed = simplexml_load_file($queueFilename);
	}

	function isCompleted($task)
	{
		return self::$completed->xpath("/queue/done/task[@hash='{$task['hash']}']");
	}

	static function push($task)
	{
		$info = self::isCompleted($task);
		
		if ($info)
		{
			$task['status']	=	(string)$info[0]['status'];
			$task['time']		=	(string)$info[0]['time'];
		}

		self::$queue[] = $task;
	}

	static function run()
	{
		$stats['tasks']			=	count($tasks);
		$stats['completed']		=	0;
		$stats['failed']		=	0;

		$queueLength			=	0;
		$queueCounter			=	1;

		if (self::$queue) foreach(self::$queue as $queueItem)
		{
			if ($queueItem['status'] == 'new')
			{
				$queueLength++;
			}
		}

		if (self::$queue) foreach(self::$queue as $queueItem)
		{
			if ($queueItem['status'] != 'new')
			{
				continue;
			}

			if (conf::i()->comet['enabled'])
			{
				$cometItem	=	array(
					'total'			=>	$queueLength,
					'hash'			=>	session::get('cometRunHash'),
					'current'		=>	$queueCounter++,
					'description'	=>	$queueItem['description'],
				);

				comet::push(1, json_encode($cometItem));
			}
			
			$output = array();
			$r = exec($queueItem['run'], $output, $error);

			$statsItem['hash']		= $queueItem['hash'];
			$statsItem['error']		= $error;
			$statsItem['output']	= nl2br(trim(implode ("\n", $output)));

			$stats['items'][]		= $statsItem;

			if ($error)
			{
				$stats['failed']++;

				if (!$queueItem['ignore_error'])
				{
					break;
				}

				$errors[]	=	$task;
			}
			else
			{
				$stats['completed']++;
				if ($queueItem['once'])
				{
					$completed[] = $queueItem;
				}
			}
		}

		self::changeStatus($completed, 'completed');
		
		return $stats;
	}

	static function changeStatus($tasks, $status)
	{
		if (!is_array($tasks))
		{
			$tasks = array($tasks);
		}

		foreach($tasks as $task)
		{
			if (is_array($task))
			{
				$hash = $task['hash'];
			}
			else
			{
				$hash = $task;
			}

			$node = self::$completed->xpath("/queue/done/task[@hash='{$hash}']");

			switch ($status)
			{
				case 'completed':
				case 'ignored':
					if (!$node)
					{
						$child = self::$completed->done->addChild('task');
						$child->addAttribute('status', $status);
						$child->addAttribute('hash', $hash);
						$child->addAttribute('time', time());
					}
					break(1);
				case 'new':
					if ($node)
					{
						$dom	=	dom_import_simplexml($node[0]);
						$dom->parentNode->removeChild($dom);
					}
					break(1);
			}
		}

		self::save();
	}

	static function save()
	{
		$queueFilename = deploymentPlan::parse('${system::plan}') . '/' . self::$release .'/' . self::$space . '-' . self::$plan . '.queue';
		file_put_contents($queueFilename, self::$completed->asXML());
	}
}
?>
