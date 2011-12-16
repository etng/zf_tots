<?php
class Model_Node_Rowset extends Et_Db_Table_Rowset
{
    protected function _loadAndReturnRow($position)
    {
        if (!isset($this->_data[$position])) {
            require_once 'Zend/Db/Table/Rowset/Exception.php';
            throw new Zend_Db_Table_Rowset_Exception("Data for provided position does not exist");
        }

        // do we already have a row object for this position?
        if (empty($this->_rows[$position])) {
            $type = $this->_data[$this->_pointer]['type'];
            $rowClass = Model_Node::row($type);
            if(empty($rowClass))
            {
                $rowClass = $this->_rowClass;
            }
            $this->_rows[$position] = new $rowClass(
                array(
                    'table'    => $this->_table,
                    'data'     => $this->_data[$position],
                    'stored'   => $this->_stored,
                    'readOnly' => $this->_readOnly
                )
            );
        }

        // return the row object
        return $this->_rows[$position];
    }
    /**
     * 对数据进行排序
     *
     * @param string $filed
     * @param string $sort
     * @return void
     */
    protected function sort($cmpFunc)
    {
        usort($this->_data, $cmpFunc);
        $this->rewind();
        return $this;
    }
    public function subset($start = 0, $length = 10, $use_origin=true)
    {
        if(!$this->_orig_data)
        {
            $this->_orig_data = $this->_data;
        }
        $this->_data = array_slice($use_origin?$this->_orig_data:$this->_data, $start, $length);
        $this->_count = count($this->_data);
        $this->rewind();
        return $this;
    }
    protected function filter($filterFunc)
    {
        foreach($this->_data as $i=>$row)
        {
            if(!$filterFunc($row))
            {
                unset($this->_data[$i]);
            }
        }
        $this->_count = count($this->_data);
        $this->rewind();
        return $this;
    }
}
