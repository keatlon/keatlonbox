<?php

class acl
{
	protected static	$acl		=	false;
	protected static	$permission	=	false;

	static function init()
	{
		self::$acl	=  conf::$conf['acl'];
		self::parse();
	}

	static function check()
	{
		if(self::$permission == 'deny')
		{
			throw new accessDeniedException;
		}
	}

	static function parse()
	{
		foreach (self::$acl as $pattern => $plainRules)
		{
			$rules	=	explode(',', $plainRules);

			foreach ($rules as $rule)
			{
				list($app, $module, $action)	=	explode('.', $pattern);
				list($operation, $role)			=	explode('.', $rule);

				$app	=	$app ? $app : '*';
				$role	=	$role ? $role : '*';
				$module	=	$module ? $module : '*';
				$action	=	$action ? $action : '*';

				$acl[]	=	array
				(
					'app'			=>	$app,
					'module'		=>	$module,
					'action'		=>	$action,

					'operation'		=>	$operation,
					'role'			=>	$role
				);
			}
		}

		$current	=	array
		(
			'app'		=>	APPLICATION,
			'module'	=>	request::module(),
			'action'	=>	request::action(),
			'role'		=>	auth::role()
		);

		$permission	=	'deny';

		foreach ($acl as $aclItem)
		{
			$currentPattern	=	$aclItem['app']		. '.' .
								$aclItem['module']	. '.' .
								$aclItem['action']	. '.' .
								$aclItem['role'];
			
			$currentPath	=	(($aclItem['app'] == '*')	? '*' :	$current['app'])		. '.' .
								(($aclItem['module'] == '*') ? '*' :	$current['module'])	. '.' .
								(($aclItem['action'] == '*') ? '*' :	$current['action'])	. '.' .
								(($aclItem['role'] == '*')	? '*' :	$current['role']);

			if ($currentPattern == $currentPath)
			{
				$permission	=	$aclItem['operation'];
			}
		}

		self::$permission	=	$permission;

		return (bool)(self::$permission == 'allow');
	}
}