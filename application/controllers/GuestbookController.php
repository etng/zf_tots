<?php
require_once(dirname(__file__) . '/Action.php');
class GuestbookController extends Tots_Controller_Action
{
    public function indexAction()
    {
        $guestbooks = new Tots_Model_Table_Guestbook();
        $paginator = Zend_Paginator::factory($guestbooks->select()->order('id desc'));
        $paginator->setItemCountPerPage(20);
        $paginator->setPageRange(12);
        $paginator->setCurrentPageNumber($this->_getParam('page', 1));
        $this->view->guestbook_paginator = $paginator;
    }
    public function populateAction()
    {
        $comments = new Tots_Model_Table_Guestbook();
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
        $this->renderText('done');
    }
    public function signAction()
    {
        if($this->getRequest()->isXmlHttpRequest())
        {
            $this->_helper->layout->disableLayout();
        }
        $request = $this->getRequest();
        $form    = new Tots_Form_Guestbook();

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $comments = new Tots_Model_Table_Guestbook();
                $comment = $comments->createRow();
                $comment->setFromArray($form->getValues());
                $comment->save();
                return $this->_helper->redirector('index');
            }
        }

        $this->view->form = $form;
    }
}