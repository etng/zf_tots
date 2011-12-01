<?php

class Et_View_Helper_Tree extends Zend_View_Helper_Abstract
{

    public function tree($spec, $options = null)
    {
        $default = array(
            'selectMode' => 1,
            'url' => $this->view->serverUrl($this->view->url(array(
                'module' => 'screen',
                'controller' => 'index',
                'action' => 'get-tree'))));
        $options = array_merge($default, (array)$options);
        $element = new Et_Form_Element_Tree($spec, $options);
        return $element;
    }
}

?>