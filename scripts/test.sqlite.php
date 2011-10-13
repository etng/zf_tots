<?php
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));
set_include_path(implode(PATH_SEPARATOR, array(
    APPLICATION_PATH . '/../library',
    get_include_path(),
)));
require_once 'Zend/Loader/Autoloader.php';
Zend_Loader_Autoloader::getInstance();
 
// Define some CLI options
$getopt = new Zend_Console_Getopt(array(
    'env|e-s'    => 'Application environment for which to create database (defaults to development)',
    'help|h'     => 'Help -- usage message',
));
try {
    $getopt->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    // Bad options passed: report usage
    echo $e->getUsageMessage();
    return false;
}
 
// If help requested, report usage message
if ($getopt->getOption('h')) {
    echo $getopt->getUsageMessage();
    return true;
}
 
// Initialize values based on presence or absence of CLI options
$withData = $getopt->getOption('w');
$env      = $getopt->getOption('e');
define('APPLICATION_ENV', (null === $env) ? 'development' : $env);
$app_cfg_file = APPLICATION_PATH . '/configs/application.ini';
if(!is_readable($app_cfg_file))
{
    $app_cfg_file = APPLICATION_PATH . '/configs/application.ini.dist';
} 
 
// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('db');
$dbAdapter = $bootstrap->getResource('db');
 
$bugs = new Application_Model_Bugs();
 
$data = array(
    'created_on'      => date('Y-m-d'),
    'bug_description' => 'Something wrong',
    'bug_status'      => 'NEW'
);
 
$bugs->insert($data);

$data = array(
    'created_on'      => new Zend_Db_Expr('DATE()'),
    'bug_description' => 'Something wrong1',
    'bug_status'      => 'NEW'
);
$bugs->insert($data);

$data = array(
    'updated_on'      => '2007-03-23',
    'bug_status'      => 'FIXED'
);
 
$where = $bugs->getAdapter()->quoteInto('bug_id = ?', 1);
 
$bugs->update($data, $where);

$where = $bugs->getAdapter()->quoteInto('bug_id = ?', 4);
 
$bugs->delete($where);

// generally speaking, this script will be run from the command line
return true;