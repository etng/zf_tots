<?php
class Tots_Form_Guestbook extends Tots_Form_Abstract
{
    public function configure()
    {
        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Email:',
            'description'      => 'Your email address please!',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            ),
        ));

        // Add the comment element
        $this->addElement('textarea', 'comment', array(
            'label'      => 'Content:',
            'description'      => 'Please input your comment here!',
            'required'   => true,
            'rows'=>'3',
            'validators' => array(
                array('validator' => 'StringLength', 'options' => array(20, 2000))
                ),
        ));
        $this->addElement('text', 'travel_start_date', array(
            'label'      => 'Travel Start Date:',
            'description'      => 'When will you start your travel',
//            'helper'   => 'DatePicker',
            'class'=>'datepicker',
            'required'   => true,
             'defaultDate' => '+7',
             'minDate' => '+7',
            'validators' => array(),
        ));
        $this->addSubmit();
        // Add a captcha
//        $this->addElement('captcha', 'captcha', array(
//            'label'      => 'Please enter the 5 letters displayed below:',
//            'required'   => true,
//            'captcha'    => array(
//                'captcha' => 'Figlet',
//                'wordLen' => 5,
//                'timeout' => 300
//            )
//        ));


    }
}