<?php
require_once(dirname(__file__) . '/Action.php');
class GuestbookController extends Tots_Controller_Action
{
    public function indexAction()
    {
        $guestbooks = new Tots_Model_Table_Guestbook();
        $this->view->entries = $guestbooks->fetchAll();
    }

    public function signAction()
    {
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