<?php
class Et_Captcha_HumanImage extends Zend_Captcha_Image
{
    protected function _generateWord()
    {
        $wordLen = $this->getWordLen();
        $words = array('php', 'java', 'rails', 'eclipse', 'study');
        return $words[array_rand($words)];
    }
}
?>