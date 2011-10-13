<?php
abstract class Form_Abstract extends Zend_Form
{
     public function init()
    {
        // Set the method for the display form to POST
        $this->setMethod('post');
        $this->configure();


        // And finally add some CSRF protection
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'fieldset')),
            'Form'
        ));
        $this->setAttrib('class', 'form-stacked');
    }
    public function addElement($element, $name = null, $options = null)
    {
        $default_decorators = array(
            array('ViewHelper'),
            array('Errors'),
            array('Description', array('tag' => 'span', 'class' => 'help-inline')),
            array('HtmlTag', array('tag' => 'div')),
            array('Label'),
            array('decorator' => array('wrapper' => 'HtmlTag'), 'options'=>array('tag' => 'div', 'class'=>'clearfix')),
        );
        settype($options, 'array');
        if(!in_array($element, array('hash', 'submit')))
        {

            $options['decorators'] = $default_decorators;
        }elseif($element=='submit')
        {
            $options['class'] ='btn primary';

        }
        return parent::addElement($element, $name, $options);
    }
    function addSubmit($label = '')
    {
        // Add the submit button
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => $label?$label:'Submit',
        ));
    }
    function configure()
    {

    }
}