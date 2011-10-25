<?php
class Et_Controller_Action extends Zend_Controller_Action
{
    public static $message_lvls = array('warning', 'error', 'success', 'info');
    public function init()
    {
        /**
         * @var Zend_Controller_Action_Helper_FlashMessenger
         */
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->messages = $this->_flashMessenger->getMessages();
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');
    }
    public function message($message, $lvl = 'success')
    {
        $this->view->messages[] = compact('message', 'lvl');
    }
    function disableAutoRender()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }
    function renderJson($data)
    {
        $this->disableAutoRender();
        $this->getResponse()->sendHeaders();
        echo Zend_Json::encode($data);
    }
    function renderText($text)
    {
        $this->disableAutoRender();
        $this->getResponse()->sendHeaders();
        echo $text;
    }
    function flash($message, $lvl = 'success')
    {
        $params = func_get_args();
        $message = array_shift($params);
        $lvl = array_shift($params);
        $this->_flashMessenger->addMessage(compact('message', 'lvl'));
        if($params)
        {
            /**
             * @todo redirectUrl not included now
             */
            if(is_array($params[0]))
            {
                call_user_func_array(array($this, 'redirectRoute'), $params);
            
            }else 
            {
                call_user_func_array(array($this, 'redirect'), $params);
            }
        }
    }
    function getBootStrapResource($resource_name)
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if(! $bootstrap->hasResource($resource_name))
        {
            return false;
        }
        return $bootstrap->getResource($resource_name);
    }
    function log($message, $priority, $extras = null)
    {
        /**
         *
         * Enter description here ...
         * @var Zend_Log
         */
        $logger = $this->getBootStrapResource('Log');
        $logger->log($message, $priority, $extras);
    }
    function sendFile($file_path)
    {
        ob_clean();
        header('Content-Type: ' . mime_content_type($file_path));
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Cache-Control: max-age=0');
        header("Content-Length: " . filesize($file_path));
        readfile($file_path);
        @unlink($file_path);
    }
    /**
     * Redirect to another Route
     *
     * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoRoute()}.
     *
     * @param  array   $urlOptions Array of key/value pairs used to assemble URL
     * @param  string  $name
     * @param  boolean $reset
     * @param  boolean $encode
     * @return void
     */        
    function redirectRoute(array $urlOptions = array(), $name = null, $reset = false, $encode = true)
    {
        $this->_helper->redirector->gotoRoute($url, $options);
    }
    /**
     * Redirect to another Action
     *
     * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
     *
     * @param  string $action
     * @param  string $controller
     * @param  string $module
     * @param  array  $params
     * @return void
     */    
    function redirect($action, $controller = null, $module = null, array $params = array())
    {
        $this->_helper->redirector->gotoSimple($url, $options);
    }
    /**
     * Redirect to another URL
     *
     * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
     *
     * @param string $url
     * @param array $options Options to be used when redirecting
     * @return void
     */
    function redirectUrl($url, array $options = array())
    {
        $this->_helper->redirector->gotoUrl($url, $options);
    }
    function forward404Unless($assert)
    {
        if(!$assert)
        {
             $this->getResponse()->setHttpResponseCode(404);
             $this->renderText('404 - Not found');
             exit();
        }
    }

    /**
     * Translates the given string
     * returns the translation
     *
     * @param  string             $messageId Translation string
     * @param  string|Zend_Locale $locale    (optional) Locale/Language to use, identical with locale identifier, 
     * @see Zend_Locale for more information
     * @return string
     */
    public function _($messageid, $locale = null)
    {
        $translate = Zend_Registry::get('Zend_Translate');
        return $translate->_($messageid, $locale);
    }    
}