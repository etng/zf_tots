<?php
class GuestbookController extends Et_Controller_Action
{
    public function indexAction()
    {
        $guestbooks = new Model_Table_Guestbook();
        $select = $guestbooks->select()->order('id desc');
        switch ($filter_confirm = $this->_getParam('filter_confirm')) {
            case 'confirmed':
                $guestbooks->selectConfirmed($select);
            break;
            case 'unconfirmed':
                $guestbooks->selectUnConfirmed($select);
            break;              
            default:
                $filter_confirm = 'all';
            break;
        }
        $paginator = Zend_Paginator::factory($select);
        $paginator->setItemCountPerPage(20);
        $paginator->setPageRange(12);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->filter_confirm = $filter_confirm;
        $this->view->guestbook_paginator = $paginator;
    }
    public function populateAction()
    {
        $comments = new Model_Table_Guestbook();
        $base = $comments->getAdapter()->fetchOne('select count(1) from guestbook');
        $i=0;
        while($i<100)
        {
            $comment = $comments->createRow();
            $comment->setFromArray(array(
                'email' => sprintf('user%d@gmail.com', $base+$i),
                'comment' => sprintf('comment %d is very short', $base+$i),
            ));
            $comment->save();
            $i++;
        }
        $this->flash('done', 'success', 'index');
        //$this->renderText('done');
    }
    public function deleteAction()
    {
        $comments = new Model_Table_Guestbook();
        $comment = $comments->find($this->_getParam('id'))->current();
        $this->forward404Unless($comment);
        $comment->delete();
        $this->flash('comment deleted', 'success', 'index');
    }
    public function confirmAction()
    {
          $comments = new Model_Table_Guestbook();
        $comment = $comments->find($this->_getParam('id'))->current();
        $this->forward404Unless($comment);
        $comment->markConfirmed();
        $this->flash('comment confirmed', 'success', 'index');
    }
    public function editAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $this->_helper->layout->disableLayout();
        }
        $request = $this->getRequest();
        $comments = new Model_Table_Guestbook();
        $comment = $comments->find($this->_getParam('id'))->current();
        $this->forward404Unless($comment);
        $form    = new Form_Guestbook();
        
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $comment->setFromArray($form->getValues());
                $comment->save();
                $this->flash('comment saved', 'success', 'index');
            }
        }
        else
        {
            $form->populate($comment->toArray());
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
        $form    = new Form_Guestbook();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $comments = new Model_Table_Guestbook();
                $comment = $comments->createRow();
                $comment->setFromArray($form->getValues());
                $comment->save();
                $this->flash('comment saved', 'success', 'index');
            }
        }

        $this->view->form = $form;
    }
}