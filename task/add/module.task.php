<?php
class moduleAddController extends taskActionController
{
    public function execute($params)
    {
        $app    =   (string)$params[3];
        $module =   (string)$params[4];
        $action =   (string)$params[5];

		if (!is_dir(conf::$conf['rootdir'] . "/apps/$app"))
		{
            @mkdir(conf::$conf['rootdir'] . "/apps/$app");
		}

		if ($module)
		{
			@mkdir(conf::$conf['rootdir'] . "/apps/$app/$module");
			@mkdir(conf::$conf['rootdir'] . "/apps/$app/$module/action");
			@mkdir(conf::$conf['rootdir'] . "/apps/$app/$module/view");
			@mkdir(conf::$conf['rootdir'] . "/apps/$app/$module/js");
			
			touch(conf::$conf['rootdir'] . "/apps/$app/$module/js/$module.js");
		}

		if ($action)
		{
			$template	=	simplexml_load_file(conf::$conf['rootdir'] . '/core/assets/templates/action.xml');

			file_put_contents
			(
				conf::$conf['rootdir'] . "/apps/$app/$module/action/$action.action.php",
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

			touch(conf::$conf['rootdir'] . "/apps/$app/$module/view/$action.view.php");
		}
		
    }

	function usage()
	{
	}
}

?>