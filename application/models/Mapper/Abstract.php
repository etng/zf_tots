<?php
 abstract class Application_Model_Mapper_Abstract
    {
        protected $_dbTable;
		protected $_cls;
        public function setDbTable($dbTable)
        {
            if (is_string($dbTable)) {
                $dbTable = new $dbTable();
            }
            if (!$dbTable instanceof Zend_Db_Table_Abstract) {
                throw new Exception('Invalid table data gateway provided');
            }
            $this->_dbTable = $dbTable;
            return $this;
        }

        public function getDbTable()
        {
            if (null === $this->_dbTable) {
                $this->setDbTable('Application_Model_DbTable_' . $this->_cls);
            }
            return $this->_dbTable;
        }

        public function save(Application_Model_Abstract $obj)
        {
			$data = $obj->getData();
            if (null === ($id = $obj->getId())) {
                unset($data['id']);
                $this->getDbTable()->insert($data);
            } else {
                $this->getDbTable()->update($data, array('id = ?' => $id));
            }
        }

        public function find($id)
        {
            $result = $this->getDbTable()->find($id);
            if (0 == count($result)) {
                return;
            }
			$cls = $this->_cls;
            $row = $result->current();
			$obj = new $cls();
            $obj->load($row);
			return $obj;
        }

        public function fetchAll()
        {
            $resultSet = $this->getDbTable()->fetchAll();
            $entries   = array();
			$cls = $this->_cls;
            foreach ($resultSet as $row) {
                $entry = new $cls();
                $entry->load($row);
                $entries[] = $entry;
            }
            return $entries;
        }
    }