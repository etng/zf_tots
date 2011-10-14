<?php
class Et_Controller_Plugin_ModuleLayout extends Zend_Controller_Plugin_Abstract
{

    public function preDispatch(Zend_Controller_Request_Abstract $_request)
    {
        $config = Zend_Controller_Front::getInstance()->getParam('bootstrap')->getOptions();
        $moduleName = $_request->getModuleName();
        $layout = Zend_Layout::getMvcInstance();
        $moduleDir = Zend_Controller_Front::getInstance()->getModuleDirectory();
        if(isset($config[$moduleName]['resources']['layout']['layout'])){
            if($layout = $config[$moduleName]['resources']['layout']['layout']){
                $layout->setLayout($config[$moduleName]['resources']['layout']['layout']);
                $layout->setLayoutPath($moduleDir . DIRECTORY_SEPARATOR .$config[$moduleName]['resources']['layout']['layoutPath']);
            } else {
                $layout->disableLayout();
            }
        }elseif(isset($config['resources']['layout']['layout'])){
            $layout->setLayout($config['resources']['layout']['layout']);
            $filename = $config['resources']['layout']['layout'] . "." .
             $layout->getViewSuffix();
            if(file_exists($moduleDir . DIRECTORY_SEPARATOR .$config['resources']['layout']['layoutPath'] . DIRECTORY_SEPARATOR . $filename)){
                $layout->setLayoutPath($moduleDir . DIRECTORY_SEPARATOR .$config['resources']['layout']['layoutPath']);
            }else{
                $layout->setLayoutPath($config['resources']['layout']['layoutPath']);
            }
        }
    }
}
?>