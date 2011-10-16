[?php
class Form_<?php echo $cls;?> extends Et_Form
{
    public function configure()
    {
    <?php foreach($columns as $column):if($column['PRIMARY'])continue;?>
        <?php if($column['DATA_TYPE']=='int'):?>
        <?php if(preg_match('/(.+)_id$/i', $column['COLUMN_NAME'], $match)):?>
        $this->addElement('hidden', '<?php echo $column['COLUMN_NAME'];?>');
//        $this->addElement('select', '<?php echo $column['COLUMN_NAME'];?>', array(
//            'label' => 'lbl_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
//            'description' => 'desc_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
//            'required' => true,
//            'multiOptions' => array(), //Model_Table_<?php echo $tbl2cls_filter->filter($match[1]);?>::get<?php echo $cls;?>Options(),
//            'filters' => array(
//            ),
//            'validators' => array(
//            )
//        ));
        <?php else:?>

        $this->addElement('text', '<?php echo $column['COLUMN_NAME'];?>', array(
            'label' => 'lbl_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
            'description' => 'desc_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            )
        ));
        <?php endif;?>
        <?php elseif($column['DATA_TYPE']=='varchar'):?>

        $this->addElement('text', '<?php echo $column['COLUMN_NAME'];?>', array(
            'label' => 'lbl_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
            'description' => 'desc_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(0, <?php echo $column['LENGTH'];?>))
            )
        ));
        <?php elseif($column['DATA_TYPE']=='text'):?>

            $this->addElement('textarea', '<?php echo $column['COLUMN_NAME'];?>', array(
            'label' => 'lbl_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
            'description' => 'desc_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
            'required' => true,
            'rows' => 3,
            'filters' => array('StringTrim'),
            'validators' => array(
            )
        ));
        <?php elseif(strpos($column['DATA_TYPE'],'enum')===0):?>

            $this->addElement('radio', '<?php echo $column['COLUMN_NAME'];?>', array(
            'label' => 'lbl_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
            'description' => 'desc_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>',
            'required' => true,
            'multiOptions' => array(
                <?php foreach(explode(',', substr($column['DATA_TYPE'], strlen('enum(') , -1)) as $option):$option = trim($option, '\'"');?>
                '<?php echo $option;?>' => 'option_<?php echo $cls;?>_<?php echo $column['COLUMN_NAME'];?>_<?php echo $option;?>',
                <?php endforeach;?>
            ),
            'filters' => array(
            ),
            'validators' => array(
            )
            ));
        <?php endif;?>
    <?php endforeach;?>

    }
}