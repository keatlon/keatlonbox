<?php

    $sphinxConfigContent = file_get_contents(dirname(__FILE__) . "/../configuration/base/sphinx.abstract.conf");

    $sphinxConfigContent = str_replace('%%MYSQL_HOST%%',        conf::i()->database['pool'][conf::i()->sphinx['database']]['host'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%MYSQL_USER%%',        conf::i()->database['pool'][conf::i()->sphinx['database']]['user'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%MYSQL_PASSWORD%%',    conf::i()->database['pool'][conf::i()->sphinx['database']]['password'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%MYSQL_DATABASE%%',    conf::i()->database['pool'][conf::i()->sphinx['database']]['dbname'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%MYSQL_PORT%%',        '3306', $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%INDEX_STORAGE%%',     conf::i()->sphinx['storage_path'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%LOG%%',               conf::i()->sphinx['log_searchd'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%LOG_QUERY%%',         conf::i()->sphinx['log_query'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%PID_FILE%%',          conf::i()->sphinx['pid'], $sphinxConfigContent);
    $sphinxConfigContent = str_replace('%%PORT%%',              conf::i()->sphinx['port'], $sphinxConfigContent);

    file_put_contents(conf::i()->sphinx['config_path'] . '/' . ENVIRONMENT . '.sphinx.conf', $sphinxConfigContent);

?>
