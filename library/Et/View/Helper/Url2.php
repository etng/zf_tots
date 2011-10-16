<?php
class Et_View_Helper_Url2 extends Zend_View_Helper_Abstract
{

    public function url2($urlOptions = array();, $name = null)
    {
        if($urlOptions && !is_array($urlOptions))
        {
            parse_str($urlOptions, $urlOptions);
        }
        settype($urlOptions, 'array');
        $front = Zend_Controller_Front::getInstance();
        $request = $front->getRequest();
        $router = $front->getRouter();
        $urlOptions['module'] = isset($urlOptions['module']) ? $urlOptions['module'] : $request->getModuleName();
        $urlOptions['controller'] = isset($urlOptions['controller']) ? $urlOptions['controller'] : $request->getControllerName();
        $urlOptions['action'] = isset($urlOptions['action']) ? $urlOptions['action'] : $request->getActionName();

        return $router->assemble($urlOptions, $name, true, true);
    }
}
