<?php

/**
 * 类 Zend_View_Helper_Js 提供对js代码的压缩处理
 * 压缩内容：
 * - 去掉所有的换行、回车
 * - 将多个空格替换为一个空格
 * - 去掉js中的注釋
 * - 对代码进行packer2压缩处理
 *
 * @uses Zend_View_Helper_Partial
 * @package view\script\helper
 */
class Et_View_Helper_Js extends Zend_View_Helper_Partial
{

    public function js($name = null, $module = null, $model = null)
    {
        settype($names, 'array');
        $script = '';
        foreach($names as $name)
        {
            try
            {
                $script .=';' . $this->partial($name, $module, $model);
            }
            catch(Exception $e)
            {

            }
        }
        return $script;
    }

    public function pack($script, $encoding = 'Normal', $fastDecode = true, $specialChars = false)
    {
        require_once ('class.JavaScriptPacker.php');

        $packer = new JavaScriptPacker($script, $encoding, $fastDecode, $specialChars);
        $script = $packer->pack();
        return $script;
    }
}
