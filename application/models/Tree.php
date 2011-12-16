<?php
class Model_Tree extends Model_Node
{
    public static function find($type)
    {
        $table = Model_Node::resource();
        $select = $table->select()->where('type=?', $type);
        return $table->fetchRow($select);
    }
    public static function create($array, $type='tree')
    {
        $node = self::resource($type)->createRow($array);
        $node->save();
        return $node;
    }
}