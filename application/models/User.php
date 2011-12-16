<?php
class Model_User extends Model_Node
{
    public $type='user';
    public static function create($array, $type='user')
    {
        $node = self::resource($type)->createRow($array);
        $node->save();
        return $node;
    }
    protected function _insert()
    {
        parent::_insert();
        if(strlen($this->pass)!=32)
        {
            $this->pass = md5(__class__ . $this->pass);
        }
    }
}