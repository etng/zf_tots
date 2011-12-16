<?php
class Model_Node extends Et_Db_Model
{
    public static $mapTypeRowClass = array();
    public static function registerType($type, $table=null, $row=null)
    {
        self::$mapTypeRowClass[$type] = compact('table', 'row');
    }
    public static function resource($type=null)
    {
        if(isset(self::$mapTypeRowClass[$type]))
        {
            $table_cls = self::$mapTypeRowClass[$type]['table'];
        }
        if(empty($table_cls))
        {
            $table_cls = 'Model_Table_Node';
        }
        static $_cache = array();
        if(!isset($_cache[$table_cls]))
        {
            $_cache[$table_cls] = new $table_cls();
        }
        return $_cache[$table_cls];
    }
    public static function row($type=null)
    {
        if(isset(self::$mapTypeRowClass[$type]))
        {
            $row_class = self::$mapTypeRowClass[$type]['row'];
        }
        if(empty($row_class))
        {
            $row_class = __class__;
        }
        return $row_class;
    }
    // 标记为此类型的根节点
    //@todo 原来的根节点咋办？
    function markAsRoot()
    {
        Model_Tree::create(array(
            'type' => $this->type,
            'parent_id' => 0,
            'node_id' => $this->id,
        ), 'tree')->save();
    }
    function addChildren($child)
    {
        $children = func_get_args();
        foreach($children as $child)
        {
            $this->addChild($child);
        }
    }
    function addChild($child)
    {
        Model_Tree::create(array(
            'type' => $child->type,
            'parent_id' => $this->id,
            'node_id' => $child->id,
        ), 'tree')->save();
        //已增加子节点，清空子节点缓存
        $this->invalidChildrenCache();
    }
    function hasChildren($type)
    {
        return count($this->getChildrenIds($type))>0;
    }

    // 所有孩子节点，可指定类型...
    function getChildren($type)
    {
        return Model_Node::resource($type)->find($this->getChildrenIds($type));
    }
    function invalidChildrenCache()
    {
        $this->_children_ids = array();
    }
    protected $_children_ids=array();
    // 获取所有子节点id
    function getChildrenIds($type)
    {
        $cache_key = md5($type);
        if(!isset($this->_children_ids[$cache_key]))
        {
            $table = Model_Node::resource('tree');
            $select = $table->select()->from($table->info('name'), 'node_id', $table->info('schema'))->where('parent_id=?', $this->id);
            if($type)
            {
                $select->where('type = ?', $type);
            }

            $this->_children_ids[$cache_key] = $table->getAdapter()->fetchCol($select);
        }
        return $this->_children_ids[$cache_key];
    }
    public static function create($array, $type='undefined')
    {
        $array['type'] = $type;
        $node = self::resource($type)->createRow($array);
        $node->save();
        return $node;
    }
    protected function _insert()
    {
        if(array_key_exists('created', $this->_data))
        {
            $this->created = date('Y-m-d H:i:s');
        }
    }
    protected function _update()
    {
        if(array_key_exists('updated', $this->_data))
        {
            $this->updated = date('Y-m-d H:i:s');
        }
    }
}
