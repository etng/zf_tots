<?php
class Tots_Model_Guestbook extends Zend_Db_Table_Row
{
    protected $_tableClass = 'Tots_Model_Table_Guestbook';
    function _insert()
    {
        $this->created = date('Y-m-d H:i:s');
    }
}