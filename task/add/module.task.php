<?php
class moduleAddController extends taskActionController
{
    public function execute($params)
    {
        $app    =   (string)$params[3];
        $module =   (string)$params[4];
        $action =   (string)$params[5];

		if (!is_dir(ROOTDIR . "/apps/$app"))
		{
            @mkdir(ROOTDIR . "/apps/$app");
		}

		if ($module)
		{
			@mkdir(ROOTDIR . "/apps/$app/$module");
			@mkdir(ROOTDIR . "/apps/$app/$module/action");
			@mkdir(ROOTDIR . "/apps/$app/$module/view");
			@mkdir(ROOTDIR . "/apps/$app/$module/js");
			
			touch(ROOTDIR . "/apps/$app/$module/js/$module.js");
		}

		if ($action)
		{
			$template	=	simplexml_load_file(ROOTDIR . '/core/assets/templates/action.xml');

			file_put_contents
			(
                ROOTDIR . "/apps/$app/$module/action/$action.action.php",
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

			touch(ROOTDIR . "/apps/$app/$module/view/$action.view.php");
		}
		
    }

	function usage()
	{
	}
}

?>