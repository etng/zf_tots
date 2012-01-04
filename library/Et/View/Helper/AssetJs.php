<?php

class Et_View_Helper_AssetJs extends Zend_View_Helper_HeadScript
{
    protected $_regKey = 'Et_View_Helper_AssetJs';
    protected $_webroot;

    public function assetJs($mode = Zend_View_Helper_HeadScript::FILE, $spec = null, $placement = 'APPEND', array $attrs = array(), $type = 'text/javascript')
    {
        return $this->headScript($mode, $spec, $placement, $attrs, $type);
    }
    function prependFiles($files)
    {
        foreach(array_reverse($files) as $file)
        {
            $this->prependFile($file);
        }
        return $this;
    }
    function appendScripts($scripts)
    {
        foreach($scripts as $script)
        {
            $this->appendScript($script);
        }
        return $this;
    }
    protected $_pack_config=array('file'=>array(), 'script'=>array());
    function setPackConfig($pack_config)
    {
        $this->_pack_config = $pack_config;
        return $this;
    }
    protected function doCollect()
    {
        $grouped_items = array();
        foreach ($this as $item)
        {
            if (!$this->_isValid($item))
            {
                continue;
            }
            if (!empty($item->source))
            {
                $grouped_items[$this->itemAttributeString($item)]['source'][$item->type][] = $item->source;
            }
            else
            {
                if(!empty($item->attributes['src']))
                {
                    $src = $item->attributes['src'];
                    if($filename=$this->detectLocalScript($src))
                    {
                        $grouped_items[$this->itemAttributeString($item)]['src'][$item->type]['local'][] = $filename;
                    }
                    else
                    {
                        $grouped_items[$this->itemAttributeString($item)]['src'][$item->type]['external'][] = $src;
                    }
                }
            }
        }
        $this->getContainer()->exchangeArray(array());

        $conf = Zend_Registry::get('assets_conf');
         $seperate_pack = $conf['js']['seperate_pack'];
        foreach($grouped_items as $attribute_string=>$content)
        {
            if(!empty($content['src']))
            {
                foreach($content['src'] as $type=>$srcs)
                {
                    if(!empty($srcs['local']))
                    {
                        $cache_key = md5(implode(PHP_EOL, $srcs['local']));
                        $cache_url = $conf['js']['cache_prefix']. $cache_key . '.js';
                        $cache_file = $this->getWebRoot() .  $cache_url;
                        $ts = time();
                        if(file_exists($cache_file) && ((time()-($ts = filemtime($cache_file)))<$conf['timeout']))
                        {
                            /*cache hited*/
                        }
                        else
                        {
                            $cache_content ='';
                            foreach($srcs['local'] as $local_file)
                            {
                                $file_content = file_get_contents($local_file);
                                if($seperate_pack && !empty($conf['js']['pack_file']))
                                {
                                    if(substr($local_file, -strlen('.min.js'))!=='.min.js')
                                    {
                                        $file_content = $this->packJs($file_content, $conf['js']['pack_config']['file']);
                                    }
                                }
                                if($conf['debug'])
                                {
                                    $cache_content .= '/* ' . basename($local_file).' */' . PHP_EOL;
                                }
                                $cache_content .= $file_content . PHP_EOL;
                            }
                            if(!$seperate_pack && !empty($conf['js']['pack_file']))
                            {
                                $cache_content = $this->packJs($cache_content, $conf['js']['pack_config']['file']);
                            }
                            is_dir($d = dirname($cache_file)) || mkdir($cache_file, 0777, true);
                            file_put_contents($cache_file, $cache_content);
                        }
                        $this->appendFile(Et_Utils::public_url() . $conf['base_url'] . $cache_url .'?ts=' . $ts, $type, array());
                    }
                    if(!empty($srcs['external']))
                    {
                        foreach($srcs['external'] as $file)
                        {
                            $this->appendFile($file, $type, array());
                        }
                    }
                }
            }
            if(!empty($content['source']))
            {
                foreach($content['source'] as $type=>$items)
                {
                    $source = implode(";", $items);
                    if(!empty($conf['js']['pack_script']))
                    {
                        $source = $this->packJs($source, $conf['js']['pack_config']['script']);
                    }
                    $this->appendScript($source, $type, array());
                }
            }
        }
    }
    public function toString($indent = null)
    {
        $this->doCollect();
        return parent::toString($indent = null);
    }
    public function onlySource($indent = null)
    {
        $this->doCollect();
        $indent = (null !== $indent)
                ? $this->getWhitespace($indent)
                : $this->getIndent();

        $items = array();
        $this->getContainer()->ksort();
        foreach ($this as $item) {
            if (!$this->_isValid($item)) {
                continue;
            }
            if (!empty($item->source)) {
                  $items[] = PHP_EOL . $indent . '    ' . PHP_EOL . $item->source . $indent . '    ' . PHP_EOL . $indent;
            }
        }
        $return = implode($this->getSeparator(), $items);
        return $return;
    }
    function getWebRoot()
    {
        if(!$this->_webroot)
        {
            $this->_webroot = APPLICATION_PATH . '/../public/';
        }
        return $this->_webroot;
    }
    function detectLocalScript($src)
    {
        $base_url = Rt_Util::public_url();
        $base_path = $this->getWebRoot();
        if(strpos($src, $base_url)===0)
        {
            $filename = $base_path .substr($src, strlen($base_url));
            if(file_exists($filename))
            {

                return $filename;
            }
        }
        return false;
    }
    function itemAttributeString($item)
    {
        $attrString = '';
        if (!empty($item->attributes))
        {
            foreach ($item->attributes as $key => $value)
            {
                if (!$this->arbitraryAttributesAllowed()
                    && !in_array($key, $this->_optionalAttributes))
                {
                    continue;
                }
                // src需要压缩
                if($key=='src')
                {
                    continue;
                }
                if ('defer' == $key)
                {
                    $value = 'defer';
                }
                $attrString .= sprintf(' %s="%s"', $key, ($this->_autoEscape) ? $this->_escape($value) : $value);
            }
        }
        return $attrString;
    }
    function packJs($script, $packerConfig=array())
    {
        if(!$packerConfig)
        {
            $script = preg_replace('/([^:])\/\/((?!\n|\*\/).)*/', '$1', $script); //删除单行注释
            $script = preg_replace('/[\r\n]/', '', $script);
            $script = preg_replace('/\s{2,}/', ' ', $script);
            $script = preg_replace('/\/\*.*?\*\//', '', $script);
            return $script ;
        }
        else
        {
            $encoding = isset($packerConfig['encoding'])?$packerConfig['encoding']:'Normal';
            $fastDecode = isset($packerConfig['fastDecode'])?$packerConfig['fastDecode']:true;
            $specialChars = isset($packerConfig['specialChars'])?$packerConfig['specialChars']:false;
            if(!class_exists('JavaScriptPacker'))
            {
                require_once ('class.JavaScriptPacker.php');
            }
            $packer = new JavaScriptPacker($script, $encoding, $fastDecode, $specialChars);
            $script = $packer->pack();
            return $script;
        }
    }
}
