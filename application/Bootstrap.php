<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initCustomView()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
    }
    protected function _initModel()
    {
        $options = $this->getOptions();
        $db_options = $options['resources']['db'];
        $this->bootstrap('db');
        Et_Db_Table::$prefix = $db_options['params']['prefix'];
        if($db =  Zend_Db_Table::getDefaultAdapter())
        {
            $profiler = new Zend_Db_Profiler_Firebug('Queries runned');
            $profiler->setEnabled(true);
            $profiler->setFilterQueryType(Zend_Db_Profiler::SELECT);
            $db->setProfiler($profiler);
        }
    }
    protected function _initI18N()
    {
        $this->bootstrap('translate');
        $options = $this->getOptions();
        if(!empty($options['resources']['translate']['logUntranslated']))
        {
            $writer = new Zend_Log_Writer_Stream(APPLICATION_PATH . '/../var/log/untranslated.log');
            $logger = new Zend_Log($writer);
            $this->getResource('translate')->setOptions(array('log'=>$logger));
//            $options['resources']['translate']['log'] =$logger;
        }


    }
}