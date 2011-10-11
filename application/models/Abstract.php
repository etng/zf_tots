<?php
abstract class Application_Model_Abstract extends Zend_Db_Table_Row
{
    /**
     * 
     * @return Application_Model_Table_Abstract
     */
    abstract public static function Table();
}