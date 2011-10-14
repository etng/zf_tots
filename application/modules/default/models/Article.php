<?php
final class Tots_Model_Article extends Tots_Model_Abstract
{
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
?>