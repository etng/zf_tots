<?php

// Define path to application directory
defined('APPLICATION_PATH')|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(realpath(APPLICATION_PATH . '/../library'), get_include_path(),)));

/** Zend_Application */
require_once 'Zend/Application.php';

$app_cfg_file = APPLICATION_PATH . '/configs/application.ini';

if(!is_readable($app_cfg_file))
{
    $app_cfg_file = APPLICATION_PATH . '/configs/application.dist.ini';
}
// Create application, bootstrap, and run
$application = new Zend_Application(APPLICATION_ENV, $app_cfg_file);
$application->bootstrap()->run();