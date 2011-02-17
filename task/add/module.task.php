<?php
class moduleAddController extends taskActionController
{
    public function execute($params)
    {
		list($app, $module, $action) = explode(':', $params[4]);

		if (!is_dir(conf::i()->rootdir . "/apps/$app"))
		{
			return;
		}

		if ($module)
		{
			@mkdir(conf::i()->rootdir . "/apps/$app/$module");
			@mkdir(conf::i()->rootdir . "/apps/$app/$module/action");
			@mkdir(conf::i()->rootdir . "/apps/$app/$module/view");
			@mkdir(conf::i()->rootdir . "/apps/$app/$module/js");
			
			touch(conf::i()->rootdir . "/apps/$app/$module/js/$module.js");
		}

		if ($action)
		{
			$template	=	simplexml_load_file(conf::i()->rootdir . '/core/builder/action.xml');

			file_put_contents
			(
				conf::i()->rootdir . "/apps/$app/$module/action/$action.action.php",
				str_replace
				(
					array
					(
						'%ACTION%',
						'%MODULE%'
					),
					array
					(
						$action,
						ucfirst($module),
					),
					$template->body
				)
			);

			touch(conf::i()->rootdir . "/apps/$app/$module/view/$action.view.php");
		}
		
    }

	function usage()
	{
	}
}

?>