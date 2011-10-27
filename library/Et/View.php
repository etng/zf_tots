<?php
class Et_View extends Zend_View
{
    function _($messageId) 
    {
        $args = func_get_args();
        return call_user_func_array(array($this, 'translate'), $args);
    }
    function e($s) {
        return $this->escape($s);
    }
}