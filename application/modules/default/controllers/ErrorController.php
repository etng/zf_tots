<?php
class ErrorController extends Et_Controller_Action
{
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        if(! $errors)
        {
            $this->view->message = 'You have reached the error page';
            return;
        }
        switch($errors->type)
        {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $priority = Zend_Log::NOTICE;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $priority = Zend_Log::CRIT;
                $this->view->message = 'Application error';
                break;
        }
        // Log exception, if logger available
        $this->log($this->view->message, $priority, $errors->exception);
        $this->log('Request Parameters', $priority, $this->getRequest()->getParams());
        // conditionally display exceptions
        if($this->getInvokeArg('displayExceptions') == true)
        {
            $this->view->exception = $errors->exception;
        }
        $this->view->request = $errors->request;
    }
}