<?php
class Model_Guestbook extends Et_Db_Model
{
    protected $_tableClass = 'Model_Table_Guestbook';
    function _insert()
    {
        $this->created = date('Y-m-d H:i:s');
    }
}