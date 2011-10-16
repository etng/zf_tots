[?php
class <?php echo $cls;?>Controller extends Et_Controller_Action
{
    public function indexAction()
    {
        $<?php echo $table_name;?>s = new Model_Table_<?php echo $cls;?>();
        $select = $<?php echo $table_name;?>s->select()->order('id desc');
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(20);
        $paginator->setPageRange(12);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view-><?php echo $table_name;?>_paginator = $paginator;
    }
    public function populateAction()
    {
        $<?php echo $table_name;?>s = new Model_Table_<?php echo $cls;?>();
        $base = $<?php echo $table_name;?>s->getAdapter()->fetchOne('select count(1) from <?php echo Et_Db_Table::prefix($table_name);?>');
        $i=0;
        while($i<100)
        {
            $<?php echo $table_name;?> = $<?php echo $table_name;?>s->createRow();
            $<?php echo $table_name;?>->setFromArray(array(
            <?php foreach($columns as $column):if($column['PRIMARY'])continue;?>
        <?php if($column['DATA_TYPE']=='int'):?>
        '<?php echo $column['COLUMN_NAME'];?>' => $base+$i,
        <?php elseif($column['DATA_TYPE']=='varchar' || $column['DATA_TYPE']=='text'):?>
         '<?php echo $column['COLUMN_NAME'];?>' => sprintf('<?php echo $column['COLUMN_NAME'];?>%d', $base+$i),
        <?php elseif(strpos($column['DATA_TYPE'],'enum')===0):?>
        '<?php echo $column['COLUMN_NAME'];?>' => Et_Utils::randItem(<?php echo substr($column['DATA_TYPE'], strlen('enum(') , -1);?>),
        <?php endif;?>
        <?php endforeach;?>
            ));
            $<?php echo $table_name;?>->save();
            $i++;
        }
        $this->flash('done', 'success', 'index');
    }
    public function deleteAction()
    {
        $<?php echo $table_name;?>s = new Model_Table_<?php echo $cls;?>();
        $<?php echo $table_name;?> = $<?php echo $table_name;?>s->find($this->_getParam('id'))->current();
        $this->forward404Unless($<?php echo $table_name;?>);
        $<?php echo $table_name;?>->delete();
        $this->flash('<?php echo $table_name;?> deleted', 'success', 'index');
    }
    public function viewAction()
    {
        $<?php echo $table_name;?>s = new Model_Table_<?php echo $cls;?>();
        $<?php echo $table_name;?> = $<?php echo $table_name;?>s->find($this->_getParam('id'))->current();
        $this->forward404Unless($<?php echo $table_name;?>);
        $this->view-><?php echo $table_name;?> = $<?php echo $table_name;?>;
    }
    public function editAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $this->_helper->layout->disableLayout();
        }
        $request = $this->getRequest();
        $<?php echo $table_name;?>s = new Model_Table_<?php echo $cls;?>();
        $<?php echo $table_name;?> = $<?php echo $table_name;?>s->find($this->_getParam('id'))->current();
        $this->forward404Unless($<?php echo $table_name;?>);
        $form    = new Form_<?php echo $cls;?>();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $<?php echo $table_name;?>->setFromArray($form->getValues());
                $<?php echo $table_name;?>->save();
                $this->flash('<?php echo $table_name;?> saved', 'success', 'index');
            }
        }
        else
        {
            $form->populate($<?php echo $table_name;?>->toArray());
        }
        $this->view->form = $form;
    }
    public function createAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $this->_helper->layout->disableLayout();
        }
        $request = $this->getRequest();
        $form    = new Form_<?php echo $cls;?>();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $<?php echo $table_name;?>s = new Model_Table_<?php echo $cls;?>();
                $<?php echo $table_name;?> = $<?php echo $table_name;?>s->createRow();
                $<?php echo $table_name;?>->setFromArray($form->getValues());
                $<?php echo $table_name;?>->save();
                $this->flash('<?php echo $table_name;?> saved', 'success', 'index');
            }
        }

        $this->view->form = $form;
    }
}