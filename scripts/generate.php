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
    'model|m'    => 'generate models',
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

$env      = $getopt->getOption('e');
define('APPLICATION_ENV', (null === $env) ? 'development' : $env);
$app_cfg_file = APPLICATION_PATH . '/configs/application.ini';
if(!is_readable($app_cfg_file))
{
    $app_cfg_file = APPLICATION_PATH . '/configs/application.ini.dist';
}
$application = new Zend_Application(APPLICATION_ENV, $app_cfg_file);

// Initialize and retrieve DB resource
$bootstrap = $application->getBootstrap();
$bootstrap->bootstrap('model');
$db = $bootstrap->getResource('db');


if($getopt->getOption('m'))
{
    $tbl2cls_filter   = new Zend_Filter_Word_UnderscoreToCamelCase();
    $mvc_filter   = new Zend_Filter_Word_UnderscoreToDash();
    $tables = $db->listTables();
    $prefix = Et_Db_Table::$prefix;

    $view_tpls = glob(dirname(__file__) .'/tpl/view/*.phtml');
    $nav_links = '';
    foreach($tables as $table)
    {
        $table_name = $table;
        if(strpos($table_name, $prefix)===0)
        {
            $table_name = substr($table, strlen($prefix));
            $cls = $tbl2cls_filter->filter($table_name);
            echo $table_name . ' => ' .$cls. PHP_EOL;
            $columns = $db->describeTable($table);
            $labels = array();
            $model_filename = APPLICATION_PATH . '/models/' . $cls . '.php';
            $table_filename = APPLICATION_PATH . '/models/Table/' . $cls . '.php';
            $form_filename = APPLICATION_PATH . '/forms/' . $cls . '.php';
            $controller_filename = APPLICATION_PATH . '/modules/default/controllers/' . $cls . 'Controller.php';
            ob_start();
            include('tpl/model.php');
            $model_content = ob_get_clean();
            ob_start();
            include('tpl/form.php');
            $form_content = ob_get_clean();
            ob_start();
            include('tpl/table.php');
            $table_content = ob_get_clean();
            ob_start();
            include('tpl/controller.php');
            $controller_content = ob_get_clean();
            saveTplResult($model_filename, $model_content );
            saveTplResult($table_filename, $table_content );
            saveTplResult($form_filename, $form_content);
            saveTplResult($controller_filename, $controller_content);
            $view_dir = APPLICATION_PATH . '/modules/default/views/scripts/'.$mvc_filter->filter($table_name);;

               is_dir($view_dir) || mkdir($view_dir, 0777, true);
            foreach($view_tpls as $view_tpl)
            {
                ob_start();
                include($view_tpl);
                $view_content = ob_get_clean();
                saveTplResult($view_dir . '/' . basename($view_tpl), $view_content);
            }
            ob_start();
            include('tpl/nav.php');
            $nav_links .=ob_get_clean();
            foreach($columns as $column)
            {
                if($column['PRIMARY'])continue;
                $labels []= "lbl_{$cls}_{$column['COLUMN_NAME']}";
                $labels []= "desc_{$cls}_{$column['COLUMN_NAME']}";
                if(strpos($column['DATA_TYPE'],'enum')===0)
                {
                    foreach(explode(',', substr($column['DATA_TYPE'], strlen('enum(') , -1)) as $option)
                    {
                        $option = trim($option, '\'"');
                        $labels []= "option_{$cls}_{$column['COLUMN_NAME']}_{$option}";
                    }
                }
            }
            $labels = array_unique($labels);
            if($labels)
            {
                file_put_contents(APPLICATION_PATH . '/../data/language/zh_CN/'.$table_name.'.php', sprintf('<?php return %s;', var_export(array_combine($labels, $labels), true)));
            }
        }

    }
    saveTplResult(APPLICATION_PATH . '/modules/default/views/scripts/auto.phtml', $nav_links);
}

function saveTplResult($filename, $content)
{
    file_put_contents($filename, str_replace(array('[?php', '?]'), array('<?php', '?>'), $content ));
}
return true;