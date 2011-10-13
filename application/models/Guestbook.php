<?php
class Model_Guestbook extends Model_Abstract
{
    protected $_tableClass = 'Model_Table_Guestbook';
    function _insert()
    {
        $this->created = date('Y-m-d H:i:s');
    }
}