<?php
class Et_View_Helper_AssetCss extends Zend_View_Helper_HeadLink
{
    /**
     * Registry key for placeholder
     * @var string
     */
    protected $_regKey = 'Et_View_Helper_AssetCss';
    protected $_web_root;
    public function assetCss(array $attributes = null, $placement = Zend_View_Helper_Placeholder_Container_Abstract::APPEND)
    {
        return parent::headLink($attributes, $placement);
    }
    function prependFiles($hrefs, $media='screen', $conditionalStylesheet=null, $extras=array())
    {
        settype($hrefs, 'array');
        foreach(array_reverse($hrefs) as $href)
        {
            $this->prependStylesheet($href, $media, $conditionalStylesheet, $extras);
        }
        return $this;
    }
    public function toString($indent = null)
    {
        $grouped_items = array();
        $all_attributes = array();
        foreach ($this as $item)
        {
            if(!is_object($item))
            {
                continue;
            }
            $attributes = (array)$item;
            $href = @$attributes['href'];
            unset($attributes['href']);
            $key = serialize($attributes);
            $all_attributes[$key]=$attributes;
            if($filename=$this->detectLocalCss($href))
            {
                $grouped_items[$key]['local'][] = $filename;
            }
            else
            {
                $grouped_items[$key]['external'][] = $href;
            }
        }
        $conf = Zend_Registry::get('assets_conf');
        $this->getContainer()->exchangeArray(array());
        $base_path = realpath($this->getWebRoot());
        $debug = $conf['debug'];
        foreach($grouped_items as $key=>$hrefs)
        {
            extract($all_attributes[$key]);
            if(!empty($hrefs['local']))
            {
                $cache_key = md5(implode(PHP_EOL, $hrefs['local']));
                $cache_url = $conf['css']['cache_prefix'] . $cache_key . '.css';
                $cache_file = $base_path .'/'.  $cache_url;
                $ts = time();
                if(file_exists($cache_file) && ((time()-($ts = filemtime($cache_file)))<$conf['timeout']))
                {
                    /*cache hited*/
                }
                else
                {
                    $cache_content ='';

                    foreach($hrefs['local'] as $local_file)
                    {
                        $file_content = file_get_contents($local_file);
                        $rel_path = str_replace('\\', '/', substr(dirname(realpath($local_file)), strlen($base_path)));
                        $i=0;
                        while(preg_match_all('/@import\s+(url\()?(["\']?)([^);]+)\2(\))?\s*;/i', $file_content, $matches, PREG_SET_ORDER))
                        {
                            foreach($matches as $match)
                            {
                                $url = $this->cssUrl($match[3], $rel_path);
                                if(file_exists($import_filename = $base_path . $url))
                                {
                                    $import_file_content = '';
                                    if($debug )
                                    {
                                        $import_file_content .= '/* ' . $url . ' */' . PHP_EOL;
                                    }
                                    $import_file_content .= file_get_contents($import_filename) . PHP_EOL;
                                    $file_content = str_replace($match[0], $import_file_content , $file_content);
                                }
                            }
                            if($i++>5)break;
                        }

                        $file_content = preg_replace('/@charset ".*";/i', '', $file_content);
                        if(preg_match_all('/url\((["\']?)([^)]+)\1\)/i', $file_content, $matches, PREG_SET_ORDER))
                        {
                            foreach($matches as $match)
                            {
                                $url = $this->cssUrl($match[2], $rel_path);
                               if(file_exists($src_filename = $base_path . $url))
                                {
                                    $url = Et_Utils::public_url($url);

                                    if($url != $match[2])
                                    {
                                        $file_content = str_replace($match[0], "url(\"{$url}\")", $file_content);

                                    }
                                }
                            }
                        }
                        if($debug )
                        {
                            $cache_content .= '/* ' . basename($local_file).' */' . PHP_EOL;
                        }
                        $cache_content .=  $file_content;
                    }
                    is_dir($d = dirname($cache_file)) || mkdir($cache_file, 0777, true);
                    file_put_contents($cache_file, $cache_content);
                    if(!empty($conf['css']['pack']))
                    {
                        $this->packCSS($cache_file, $cache_file);
                    }
                }
                $this->appendStylesheet(Et_Utils::public_url() . $conf['base_url'] . $cache_url .'?ts='.$ts, $media, $conditionalStylesheet, $extras);
            }
            if(!empty($hrefs['external']))
            {
                foreach($hrefs['external'] as $href)
                {
                    $this->appendStylesheet($href, $media, $conditionalStylesheet, $extras);
                }
            }
        }
        return parent::toString($indent = null);
    }
    protected function packCSS($input_file, $output_file)
    {
        $old_cwd = getcwd();
        chdir($root_path = realpath(APPLICATION_PATH . '/../'));
        $input_file = str_replace('/', DIRECTORY_SEPARATOR, substr($input_file, strlen($root_path)+1));
        $output_file = str_replace('/', DIRECTORY_SEPARATOR, substr($output_file, strlen($root_path)+1));
        $command = "java -jar bin\yuicompressor.jar -v {$input_file} -o {$output_file}";
        exec($command, $ouput);
        chdir($old_cwd);
    }
    protected function cssFile($url, $base_path)
    {
        if(in_array(substr($url, 0, 7), array('https:/', 'http://')))
        {
        }
        elseif(substr($url, 0, 3)=='../')
        {
            $url = dirname($base_path) .''. substr($url, 2);
        }
        elseif(substr($url, 0, 2)=='./')
        {
            $url = $base_path . substr($url, 1);
        }
        else
        {
            $url = $base_path .'/'. $url;
        }
        return $url;
    }
    protected function cssUrl($url, $base_url)
    {
        if(in_array(substr($url, 0, 7), array('https:/', 'http://')))
        {
        }
        elseif(substr($url, 0, 3)=='../')
        {
            $url = dirname($base_url) .''. substr($url, 2);
        }
        elseif(substr($url, 0, 2)=='./')
        {
            $url = $base_url . substr($url, 1);
        }
        else
        {
            $url = $base_url .'/'. $url;
        }
        return $url;
    }
    protected $_webroot;
    function getWebRoot()
    {
        if(!$this->_webroot)
        {
            $this->_webroot = APPLICATION_PATH . '/../public/';
        }
        return $this->_webroot;
    }
    function detectLocalCss($src)
    {
        $base_url = Et_Utils::public_url();
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
}
