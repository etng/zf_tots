<?php
class Model_Table_Guestbook extends Model_Table_Abstract
{
    protected $_name = 'guestbook';
    protected $_rowClass = 'Model_Guestbook';
    function selectConfirmed($select)
    {
         $select->where("confirmed!='0000-00-00 00:00:00'");
    }
    function selectUnConfirmed($select)
    {
        $select->where("confirmed='0000-00-00 00:00:00'");
    }
    public static function getCategoryPairs()
    {
        return array(
            '1' => 'Zend Framework',
        '2' =>'ThinkPHP',
        '3' => 'CakePHP',
        '4' => 'Yii',
        '5' => 'Symfony',
        );
    }
}
?>