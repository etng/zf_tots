<?php
require_once (dirname(__file__) . '/Action.php');
class DemoController extends Tots_Controller_Action
{
    public function logAction()
    {
        $this->log('DEBUG Message', Zend_Log::DEBUG);
        $this->log('Info Message', Zend_Log::INFO);
        $this->log('Notice Message', Zend_Log::NOTICE);
        $this->log('WARN Message', Zend_Log::WARN);
        $this->log('ERR Message', Zend_Log::ERR);
        $this->log('CRIT Message', Zend_Log::CRIT);
        $this->log('ALERT Message', Zend_Log::ALERT);
        $this->log('EMERG Message', Zend_Log::EMERG);
        $this->renderText('Please Use Firefox and FirePHP extension to view the log message!');
    }
}

