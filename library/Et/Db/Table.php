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
}
?>