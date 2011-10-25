<?php
class DemoController extends Et_Controller_Action
{
    public function logAction()
    {
        echo '<pre>';
        var_dump($this->_getAllParams());
        die();
        $this->log('DEBUG Message', Zend_Log::DEBUG);
        $this->log('Info Message', Zend_Log::INFO);
        $this->log('Notice Message', Zend_Log::NOTICE);
        $this->log('WARN Message', Zend_Log::WARN);
        $this->log('ERR Message', Zend_Log::ERR);
        $this->log('CRIT Message', Zend_Log::CRIT);
        $this->log('ALERT Message', Zend_Log::ALERT);
        $this->log('EMERG Message', Zend_Log::EMERG);
        $this->renderText('Please Use Firefox and FirePHP extension to view the log message!');
    }
    public function formAction()
    {
        //        $this->_helper->layout->setLayout('layout');
        $form = new Zend_Form();
        $price_decorators = array(
        'ViewHelper', 
        array('Description', array('tag' => 'span', 'class' => 'help-inline')), 
        array('HtmlTag', array('tag' => 'div')), 
        'Label',  array(
                'decorator' => array('wrapper' => 'HtmlTag'),
                'options' => array('tag' => 'div', 'class' => 'clearfix')));
        $form->addElement('text', 'public_price', array(
        'label' => 'Market Price', 
        'decorators' => $price_decorators));
        $form->addElement('text', 'min_price', array(
        'label' => 'Min Price', 
        'decorators' => $price_decorators));
        $form->addElement('text', 'max_price', array(
        'label' => 'Max Price', 
        'decorators' => $price_decorators));
        $form->addElement('text', 'default_price', array(
        'label' => 'Price', 
        'decorators' => $price_decorators));
        $form->addDisplayGroup(array(
        'public_price', 
        'min_price', 
        'max_price', 
        'default_price'), 'pricing', array(
        'legend' => 'pricing', 
        'decorators' => array('FormElements', 'Fieldset')));
        $form->createElement('text', 'username');
        // Create and configure username element:
        $username = $form->createElement('text', 'username');
        $username->addValidator('alnum')
            ->addValidator('regex', false, array('/^[a-z]+/'))
            ->addValidator('stringLength', false, array(6, 20))
            ->setRequired(true)
            ->addFilter('StringToLower');
        // Create and configure password element:
        $password = $form->createElement('password', 'password');
        $password->addValidator('StringLength', false, array(6))->setRequired(true);
        // Add elements to form:
        $form->addElement($username)
            ->addElement($password)
            ->addElement('submit', 'login', array('label' => 'Login'));
        if($this->getRequest()->isPost())
        {
            if($form->isValid($this->getRequest()
                ->getPost()))
            {
                Et_Utils::edump($form->getValues());
                $this->flash('comment saved', 'success', 'index');
            }
        }
        $this->view->form = $form;
    }
}