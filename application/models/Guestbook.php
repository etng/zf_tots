<?php
class Model_Guestbook extends Et_Db_Model
{
    protected $_tableClass = 'Model_Table_Guestbook';
    function _insert()
    {
        $this->created = date('Y-m-d H:i:s');
    }
        function isConfirmed()
    {
        return $this->confirmed!='0000-00-00 00:00:00';
    }
    function markConfirmed()
    {
        $this->confirmed = date('Y-m-d H:i:s');
        $this->save();
    }
    function markUnConfirmed()
    {
        $this->confirmed = '0000-00-00 00:00:00';
        $this->save();
    }

}