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
    /**
     * Save a row to the database
     *
     * @param array             $data The data to insert/update
     * @param Zend_DB_Table_Row $row Optional The row to use
     * @return mixed The primary key
     */
    public function saveRow($data, $row = null)
    {
        if (null === $row) {
            $row = $this->createRow();
        }

        $columns = $this->info('cols');
        foreach ($columns as $column) {
            if (array_key_exists($column, $info)) {
                $row->$column = $data[$column];
            }
        }

        return $row->save();
    }
    public function truncate()
    {
        return $this->getAdapter()->query(sprintf('TRUNCATE TABLE `%s`', $this->info('name')));
    }
    public function count($where=array())
    {
        $select = $this->select()->from($this->info('name'), new Zend_Db_Expr('count(1)'), $this->info('schema'));
        foreach($where as $cond=>$value)
        {
            $select->where($cond, $value);
        }
        return $this->getAdapter()->fetchOne($select);
    }
}
?>