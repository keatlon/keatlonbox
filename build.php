<?php

date_default_timezone_set('Europe/Helsinki');

require_once dirname(__FILE__) . "/system/sys.php";

$arguments = __parseArguments($argv);

if (!$arguments)
{
    printUsage();
    exit (1);
}

if (!isset($arguments['target']))
{
    printUsage();
    exit (1);
}

$targets = explode(',', $arguments['target']);

!defined('ROOTDIR') ? define('ROOTDIR', realpath(dirname(__FILE__) . '/../')) : false;

if (isset($arguments['confdir']))
{
    define('CONFDIR', $arguments['confdir']);
}

define('PRODUCT', isset($arguments['product']) ? $arguments['product'] : 'default');

include dirname(__FILE__) . "/conf/init.php";

$cacheDir = ROOTDIR . conf::$conf['cachedir'];

if (!is_dir($cacheDir))
{
    mkdir($cacheDir);
}

foreach ($targets as $target)
{
    switch ($target)
    {
        case 'autoload':
            autoload(ROOTDIR);
            break;

        case 'db':
            database($arguments);
            break;

        case 'form':
            forms(ROOTDIR, $app);
            break;

        case 'static':

            $conf = include ROOTDIR . '/conf/' . PRODUCT . '.static.php';

            foreach ($conf as $group => $files)
            {
                $info = pathinfo($group);

                switch ($info['extension'])
                {
                    case 'css':
                        resource::build($group, 'css');
                        break;

                    case 'js':
                        resource::build($group, 'js');
                        break;
                }
            }

            break;
    }
}

function printError($error)
{
    echo "
***********************************************************\n
ERROR: " . $error . "\n
***********************************************************\n
";
}

function printUsage()
{
    echo
    "
-----------------------------------------------------------
USAGE: build.php options
-----------------------------------------------------------
--product	 product name. Default `default`
--target	 targets to process (comma separated)
--confdir	 directory with configuration files
-----------------------------------------------------------
possible targets
1) core
2) db
3) apps
";
}

function database($arguments)
{
    $alias  = isset($arguments['alias']) ? $arguments['alias'] : false;
    $prefix = isset($arguments['prefix']) ? $arguments['prefix'] : '';

    if (isset($arguments['tables']))
    {
        $tables = explode(',', $arguments['tables']);

        foreach ($tables as $tableName)
        {
            table($tableName, $prefix, $alias);
        }

        echo count($tables) . " tables found \n";

        return true;
    }

    foreach (conf::$conf['database']['pool'] as $currentAlias => $dbConnection)
    {
        if ($alias && $alias != $currentAlias)
        {
            continue;
        }

        $dbName =   conf::$conf['database']['pool'][$currentAlias]['dbname'];
        $prefix =   isset(conf::$conf['database']['pool'][$currentAlias]['prefix']) ? conf::$conf['database']['pool'][$currentAlias]['prefix'] : $prefix;
        $tables = db::cols('SHOW TABLES FROM `' . $dbName . '`', array(), $currentAlias);

        foreach ($tables as $tableName)
        {
            table($tableName, $prefix, $currentAlias);
        }

        echo count($tables) . " tables found in $currentAlias\n";
    }
}

function table($tableName, $prefix = '', $alias = 'master')
{
    $modelPath = ROOTDIR . '/lib/model';
    $parts     = explode('_', $tableName);

    if ($prefix)
    {
        array_unshift($parts, $prefix);
    }

    if (count($parts) > 1)
    {
        for ($l = 1; $l < count($parts); $l++)
        {
            $parts[$l] = ucfirst($parts[$l]);
        }
    }

    $className = implode('', $parts) . 'Peer';
    $classPath = $modelPath . '/' . $className . '.class.php';

    // GENERATE BASE CLASS
    $primaryKey = 'id';
    $fields     = array();

    $columns = db::rows('show columns from `' . $tableName . '`', array(), $alias);

    $primaryKeys = array();

    $primaryCField  = '';
    $primaryFields  = array();
    $primaryTFields = array();
    $primaryOFields = array();
    $primaryBinds   = array();
    $fields         = array();

    foreach ($columns as $column)
    {
        if ($column['Key'] == 'PRI')
        {
            $primaryKeys[] = $column['Field'];
        }

        $fields[] = "'" . $column['Field'] . "'";
    }

    foreach ($primaryKeys as $primaryField)
    {
        $primaryFields[]  = "'" . $primaryField . "'";
        $primaryTFields[] = "'" . $tableName . "." . $primaryField . "'";
        $primaryOFields[] = "'" . $tableName . "." . $primaryField . " DESC'";

        $primaryBinds[] = " " . $primaryField . " = :" . $primaryField . " ";
    }

    $primaryCKey  = isset($primaryFields[0]) ? $primaryFields[0] : "''";
    $primaryKey   = "array(" . implode(',', $primaryFields) . ")";
    $primaryTKey  = "array(" . implode(',', $primaryTFields) . ")";
    $primaryOKey  = "array(" . implode(',', $primaryOFields) . ")";
    $primaryBind  = implode('AND', $primaryBinds);
    $multiPrimary = count($primaryKeys) > 1 ? 'true' : 'false';

    $baseClassName    = implode('', $parts) . 'BasePeer';
    $baseClassPath    = $modelPath . '/base/' . $baseClassName . '.class.php';
    $xml              = simplexml_load_file(ROOTDIR . '/core/assets/templates/basePeerClass.xml');
    $baseClassContent = str_replace('%BASECLASSNAME%', $baseClassName, $xml->body);
    $baseClassContent = str_replace('%CLASSNAME%', $className, $baseClassContent);
    $baseClassContent = str_replace('%TABLENAME%', $tableName, $baseClassContent);
    $baseClassContent = str_replace('%CONNECTION%', $alias, $baseClassContent);
    $baseClassContent = str_replace('%PRIMARYKEY%', $primaryKey, $baseClassContent);
    $baseClassContent = str_replace('%PRIMARYTKEY%', $primaryTKey, $baseClassContent);
    $baseClassContent = str_replace('%PRIMARYOKEY%', $primaryOKey, $baseClassContent);
    $baseClassContent = str_replace('%PRIMARYCKEY%', $primaryCKey, $baseClassContent);
    $baseClassContent = str_replace('%PRIMARYBIND%', $primaryBind, $baseClassContent);
    $baseClassContent = str_replace('%MULTIPRIMARY%', $multiPrimary, $baseClassContent);

    $baseClassContent = str_replace('%FIELDS%', implode(',', $fields), $baseClassContent);

    file_put_contents($baseClassPath, $baseClassContent);

    $xml          = simplexml_load_file(ROOTDIR . '/core/assets/templates/peerClass.xml');
    $classContent = str_replace('%BASECLASSNAME%', $baseClassName, $xml->body);
    $classContent = str_replace('%CLASSNAME%', $className, $classContent);
    if (!file_exists($classPath))
    {
        file_put_contents($classPath, $classContent);
    }
}

function autoload($rootdir)
{
    $files   = scan($rootdir, '|.*\.php$|');
    $classes = array();
    $apps    = array();

    foreach ($files as $file)
    {
        $isAction = preg_match('#/apps/([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/(.*)/([a-zA-Z0-9_]+)\.(action|class)\.php#U', $file, $matches);

        if ($isAction)
        {
            list($path, $application, $module, $type, $name, $ext) = $matches;

            $apps[$application] = true;

            if ($type == 'action')
            {
                $classes[$application][$name . ucfirst($module) . 'Controller'] = $file;
                continue;
            }

            $classes[$application][$name] = $file;
            continue;
        }

        $isCoreClass = preg_match('#/(core|lib)/(.*)\.class\.php#U', $file, $matches);

        if ($isCoreClass)
        {
            $info                               = pathinfo($matches[2]);
            $classes['core'][$info['filename']] = $file;
            continue;
        }

        $isCoreAction = preg_match('#/core/([a-zA-Z0-9_]+)/([a-zA-Z0-9_]+)/(.*)/([a-zA-Z0-9_]+)\.action\.php#U', $file, $matches);

        if ($isCoreAction)
        {
            list($path, $application, $module, $type, $name) = $matches;
            $classes['core'][$name . ucfirst($module) . 'Controller'] = $file;
            continue;
        }

        $isTask = preg_match('#/.*/(.*)/(.*)\.task\.php#', $file, $matches);

        if ($isTask)
        {
            list($path, $module, $action) = $matches;
            $classes['core'][$action . ucfirst($module) . 'Controller'] = $file;
            continue;
        }
    }

    foreach ($classes as $app => $files)
    {
        printf("%s classes in %s \n", count($files), $app);
        file_put_contents($rootdir . "/~cache/autoload-" . $app . ".php", "<?php return " . var_export($files, true) . ';');
    }
}

function forms($rootdir, $application)
{
    $formPath = ROOTDIR . '/lib/form';
    $views    = scan($rootdir . "/apps/" . $application, '|.*\.view\.php|');
    $template = simplexml_load_file(ROOTDIR . '/core/assets/templates/form.xml');

    foreach ($views as $filename)
    {
        $content = file_get_contents($filename);
        $res     = preg_match_all('|<form.*action=[\'"]{1}(.*)[\'"]{1}.*>|U', $content, $matches);

        if (!$matches[1])
        {
            continue;
        }

        foreach ($matches[1] as $action)
        {
            $classname     = implode('', array_map('ucfirst', explode('/', $action))) . 'form';
            $classname{0}  = strtolower($classname{0});
            $classFilename = $formPath . '/' . $classname . '.class.php';

            if (file_exists($classFilename))
            {
                continue;
            }

            file_put_contents($classFilename, str_replace('%BASECLASSNAME%', $classname, $template->body));
        }
    }
}

function sphinx($rootdir, $application)
{
    include dirname(__FILE__) . "/../conf/init.php";

    $sphinxConfigContent = file_get_contents(dirname(__FILE__) . "/../../conf/sphinx.abstract.conf");

    $sphinxConfigContent = str_replace('%%MYSQL_HOST%%', conf::$conf['database']['pool'][conf::$conf['sphinx']['database']]['host'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%MYSQL_USER%%', conf::$conf['database']['pool'][conf::$conf['sphinx']['database']]['user'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%MYSQL_PASSWORD%%', conf::$conf['database']['pool'][conf::$conf['sphinx']['database']]['password'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%MYSQL_DATABASE%%', conf::$conf['database']['pool'][conf::$conf['sphinx']['database']]['dbname'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%MYSQL_PORT%%', '3306', $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%INDEX_STORAGE%%', conf::$conf['sphinx']['storage_path'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%LOG%%', conf::$conf['sphinx']['log_searchd'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%LOG_QUERY%%', conf::$conf['sphinx']['log_query'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%PID_FILE%%', conf::$conf['sphinx']['pid'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%PORT%%', conf::$conf['sphinx']['port'], $sphinxConfigContent);

    file_put_contents(conf::$conf['sphinx']['config_path'] . '/sphinx.' . ENVIRONMENT . '.conf', $sphinxConfigContent);
}
