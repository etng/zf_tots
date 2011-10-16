<?php
abstract class Et_Db_Table extends Zend_Db_Table
{
    public static $prefix='';
    public function init()
    {
    }
    public static function prefix($table)
    {
        return self::$prefix.$table;
    }

}
?>