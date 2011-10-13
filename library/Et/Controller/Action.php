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
    public function message($message, $lvl='success') {
        $this->view->messages[]=compact('message', 'lvl');
    }
    function disableAutoRender()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }
    function renderJson($data)
    {
        $this->disableAutoRender();
        echo Zend_Json::encode($data);
    }
    function renderText($text)
    {
        $this->disableAutoRender();
        echo $text;
    }
    function flash($message, $lvl='success', $new_url = null)
    {
        $this->_flashMessenger->addMessage(compact('message', 'lvl'));
        if($new_url)
        {
            $this->_redirect($new_url);
        }
    }
    function getBootStrapResource($resource_name)
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasResource($resource_name)) {
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
        header('Content-Disposition: attachment; filename="' . basename($file_path) .'"');
        header('Cache-Control: max-age=0');
        header("Content-Length: " . filesize($file_path));
        readfile($file_path);
        @unlink($file_path);
    }
}
?>