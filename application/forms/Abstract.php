<?php
abstract class Form_Abstract extends Zend_Form
{
    public function getDecorators()
    {
        if(!$this->_decorators)
        {
            $this->addDecorators(array(
                'FormElements',
                array('HtmlTag', array('tag' => 'fieldset')),
                'Form'
            ));
        }
        return parent::getDecorators();
    }
     public function init()
    {
        $this->addPrefixPath('ZendX_JQuery_Form', 'ZendX/JQuery/Form/');
        $this->addPrefixPath('Et_Form', 'Et/Form/');
        $this->setMethod('post');
        $this->setAttrib('class', 'form-stacked');
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
        ));
        $this->configure();
    }
    public function addElement($element, $name = null, $options = null)
    {
        settype($options, 'array');
        if(in_array($element, array('captcha', 'humanCaptcha')))
        {
             $options['decorators'] = array(
            array('Errors'),
            array('Description', array('tag' => 'span', 'class' => 'help-inline')),
            array('HtmlTag', array('tag' => 'div')),
            array('Label'),
            array('decorator' => array('wrapper' => 'HtmlTag'), 'options'=>array('tag' => 'div', 'class'=>'clearfix')),
        );
        }elseif(in_array($element, array('autoComplete', 'colorPicker', 'datePicker', 'slider', 'spinner')))
        {
          $options['decorators'] = array(
            array('UiWidgetElement'),
            array('Errors'),
            array('Description', array('tag' => 'span', 'class' => 'help-inline')),
            array('HtmlTag', array('tag' => 'div')),
            array('Label'),
            array('decorator' => array('wrapper' => 'HtmlTag'), 'options'=>array('tag' => 'div', 'class'=>'clearfix')),
        );
        
        }elseif(in_array($element, array('submit', 'button', 'image', 'reset')))
        {
            $options['decorators'] = array(
             array('Tooltip'),
             array('ViewHelper'),
             array('decorator' => array('wrapper' => 'HtmlTag'), 'options'=>array('tag' => 'div', 'class'=>'clearfix')),
            );
            $classes = array('btn');
            if($element=='submit')
            {
                $classes[]='primary';
            }
            $options['class'] =implode(' ', $classes);
        }elseif(in_array($element, array('hash', 'hidden')))
        {
             $options['decorators'] = array(
             array('ViewHelper'),
            );            
        }
        else
        {
            $options['decorators'] = array(
            array('ViewHelper'),
            array('Errors'),
            array('Description', array('tag' => 'span', 'class' => 'help-inline')),
            array('HtmlTag', array('tag' => 'div')),
            array('Label'),
            array('decorator' => array('wrapper' => 'HtmlTag'), 'options'=>array('tag' => 'div', 'class'=>'clearfix')),
        );
        }
        return parent::addElement($element, $name, $options);
    }
    function addCaptcha($name, $label=null)
    {
                $this->addElement('humanCaptcha', $name, array(
            'label'      => $label,
            'required'   => true,
            'prefixPath' => array(
                'CAPTCHA'=>array('Et_Captcha_'=>array('Et/Captcha/'),),
            ),
            'captcha'    => array(
                'captcha' => 'HumanImage',
                'expiration'=>'3600',
                'dotNoiseLevel'=>20,
                'lineNoiseLevel'=>2,
                'imgUrl' => '/captcha/',
        		'imgDir' => APPLICATION_PATH . '/../public/captcha/',
            	'font'=>APPLICATION_PATH . '/../data/font/DroidSansMono.ttf',
                'wordLen' => 5,
                'timeout' => 300
            )
        ));
    }
    function addSubmit($label = '')
    {
        $this->addElement('submit', 'submit', array(
            'ignore'   => true,
            'label'    => $label?$label:'Submit',
        ));
    }
    function configure()
    {

    }
}