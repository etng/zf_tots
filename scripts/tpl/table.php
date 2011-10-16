[?php
class Model_Table_<?php echo $cls;?> extends Et_Db_Table
{
    protected $_name = '<?php echo $table;?>';
    protected $_rowClass = 'Model_<?php echo $cls;?>';
}