<?php
class Et_Form extends Zend_Form
{
    protected $has_submit=false;
    public function getDecorators()
    {
        if(! $this->_decorators)
        {
            $this->addDecorators(array(
                'FormElements',
            array('HtmlTag', array('tag' => 'fieldset')),
            'Form'
            ));
        }
        if(!$this->has_submit)
        {
            $this->addSubmit();
        }
        return parent::getDecorators();
    }
    public function init()
    {
        $this->addPrefixPath('ZendX_JQuery_Form', 'ZendX/JQuery/Form/');
        $this->addPrefixPath('Et_Form', 'Et/Form/');
        $this->setMethod('post');
        $this->setAttrib('class', 'form-stacked');
        $this->addElement('hash', 'csrf', array('ignore' => true));
        $this->configure();
    }
    public function addElement($element, $name = null, $options = null)
    {
        settype($options, 'array');
        switch($element)
        {
            case 'captcha':
            case 'humanCaptcha':
                $options['decorators'] = array(
                array('Errors'),
                array(
                'Description',
                array('tag' => 'span', 'class' => 'help-inline')),
                array('HtmlTag', array('tag' => 'div')),
                array('Label'),
                array(
                'decorator' => array('wrapper' => 'HtmlTag'),
                'options' => array('tag' => 'div', 'class' => 'clearfix')));
                break;
            case 'autoComplete' :
            case 'colorPicker' :
            case 'datePicker' :
            case 'slider' :
            case 'spinner':
                $options['decorators'] = array(
                array('UiWidgetElement'),
                array('Errors'),
                array(
                'Description',
                array('tag' => 'span', 'class' => 'help-inline')),
                array('HtmlTag', array('tag' => 'div')),
                array('Label'),
                array(
                'decorator' => array('wrapper' => 'HtmlTag'),
                'options' => array('tag' => 'div', 'class' => 'clearfix')));
                break;
            case 'submit':
            case 'button':
            case 'image':
            case 'reset':
                if($element== 'submit' || $element== 'image')
                {
                    $this->has_submit = true;
                }
                $options['decorators'] = array(
                array('Tooltip'),
                array('ViewHelper'),
                array(
                'decorator' => array('wrapper' => 'HtmlTag'),
                'options' => array('tag' => 'div', 'class' => 'clearfix')));
                $classes = array('btn');
                if($element== 'submit')
                {
                    $classes[] = 'primary';
                }
                $options['class'] = implode(' ', $classes);

                break;
            case 'hash' :
            case 'hidden' :
                $options['decorators'] = array(array('ViewHelper'));
                break;
            default:
                $options['decorators'] = array(
                array('ViewHelper'),
                array('Errors'),
                array(
                'Description',
                array('tag' => 'span', 'class' => 'help-inline')),
                array('HtmlTag', array('tag' => 'div')),
                array('Label'),
                array(
                'decorator' => array('wrapper' => 'HtmlTag'),
                'options' => array('tag' => 'div', 'class' => 'clearfix')));
                break;
        }

        return parent::addElement($element, $name, $options);
    }
    function addCaptcha($name, $label = null)
    {
        $this->addElement('humanCaptcha', $name, array(
            'label' => $label,
            'required' => true,
            'prefixPath' => array(
            'CAPTCHA' => array('Et_Captcha_' => array('Et/Captcha/'))),
            'captcha' => array(
            'captcha' => 'HumanImage',
            'expiration' => '3600',
            'dotNoiseLevel' => 20,
            'lineNoiseLevel' => 2,
            'imgUrl' => '/captcha/',
            'imgDir' => APPLICATION_PATH . '/../public/captcha/',
            'font' => APPLICATION_PATH . '/../data/font/DroidSansMono.ttf',
            'wordLen' => 5,
            'timeout' => 300,
            ),
            ));
    }
    function addSubmit($label = '')
    {
        $this->addElement('submit', 'submit', array(
        'ignore' => true,
        'label' => $label?$label:'Submit'));
    }
    function configure()
    {
    }
}