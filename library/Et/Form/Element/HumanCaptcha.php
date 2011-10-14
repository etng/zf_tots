<?php
class Et_Form_Element_HumanCaptcha extends Zend_Form_Element_Captcha
{
        public function setOptions(array $options)
    {
            if (isset($options['prefixPath'])) {
            $this->addPrefixPaths($options['prefixPath']);
            unset($options['prefixPath']);
        }

        parent::setOptions($options);
        return $this;
    }
    
}