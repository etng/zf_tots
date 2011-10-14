<?php
class Form_Guestbook extends Et_Form
{
    public function configure()
    {
        $this->addElement('text', 'email', array(
        'label' => 'Email:', 
        'description' => 'Your email address please!', 
        'required' => true, 
        'filters' => array('StringTrim'), 
        'validators' => array('EmailAddress')));
        $this->addElement('multiCheckbox', 'category_ids', array(
        'label' => 'Category:', 
        'description' => 'Publish article to category', 
        'required' => true, 
        'multiOptions' => Model_Table_Guestbook::getCategoryPairs(), 
        'filters' => array('StringTrim'), 
        'validators' => array()));
        $this->addElement('textarea', 'comment', array(
        'label' => 'Content:', 
        'description' => 'Please input your comment here!', 
        'required' => true, 
        'rows' => '3', 
        'validators' => array(
        array('validator' => 'StringLength', 'options' => array(5, 2000)))));
        $this->addElement('datePicker', 'published', array(
        'label' => 'Publish Date:', 
        'description' => 'When will you make this article public', 
        'class' => 'datepicker', 
        'required' => false, 
        'jqueryParams' => array(
        'defaultDate' => '+7', 
        'minDate' => '+0', 
        'maxDate' => '+100'), 
        'validators' => array()));
        $this->addCaptcha('captcha');
        $this->addSubmit();
    }
}