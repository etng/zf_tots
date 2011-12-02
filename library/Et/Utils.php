<?php
class Et_Utils
{
    public static $encoding = 'base64';
    function tempDir($prefix='')
    {
        if(($temp=getenv('TMP')) || ($temp=getenv('TEMP')) || ($temp=getenv('TMPDIR')))
        {
        }
        else
        {
          $temp=tempnam(__FILE__,'');
          if (file_exists($temp))
          {
              @unlink($temp);
              $temp = dirname($temp);
          }
        }
        if($prefix && is_dir($temp  .'/'. $prefix))
        {
            mkdir(($temp  .'/'. $prefix));
            return ($temp  .'/'. $prefix);
        }
        return $temp;
    }
    function randItem($item)
    {
        $items = func_get_args();
        return $items[array_rand($items)];
    }
    public static function cdump($args)
    {
        $args = func_get_args();
        echo PHP_EOL.'<!--' . PHP_EOL;
        call_user_func_array('var_dump', $args);
        echo PHP_EOL . '-->'.PHP_EOL;
    }
    public static function dump($args)
    {
        $args = func_get_args();
        echo '<pre>';
        call_user_func_array('var_dump', $args);
        echo '</pre>';
    }
    public static function edump($args)
    {
        $args = func_get_args();
        call_user_func_array(array('self', 'dump'), $args);
        die();
    }
    public static  function initCache($cache_name)
    {
        if(!self::$enable_cache)
        {
            return false;
        }
        if(strpos($cache_name, 'cache_')!==0)
        {
            $cache_name = 'cache_' . $cache_name;
        }
        if(!Zend_Registry::isRegistered($cache_name))
        {
            $options = Zend_Registry::get($cache_name . '_options');
            $logger = Zend_Registry::get('log');
            if($logger)
            {
                $options['frontend']['options']['logging'] = true;
                $options['frontend']['options']['logger'] = $logger;
                $cache = Zend_Cache::factory($options['frontend']['name'], $options['backend']['name'], $options['frontend']['options'], $options['backend']['options']);
            }
            Zend_Registry::set($cache_name, $cache);
        }
        return Zend_Registry::get($cache_name);
    }
    public static $enable_cache = true;
    public static  function cacheRead($cache, $ck)
    {
        if(!self::$enable_cache)
        {
            return null;
        }
        return self::initCache($cache)->load($ck);
    }
    public static  function cacheWrite($cache, $ck, $value)
    {
        if(!self::$enable_cache)
        {
            return false;
        }
        return self::initCache($cache)->save($value, $ck);
    }
    public static  function cacheRemove($cache, $ck) {
        if(!self::$enable_cache)
        {
            return false;
        }
        return self::initCache($cache)->remove($ck);
    }
    /**
     * 判断字符串是否以特定字符串结尾
     * 
     * @param string $haystack    长字符串
     * @param string $needle   短字符串
     */
    public static function endwith($haystack, $needle)
    {
        return substr($haystack, strrpos($haystack, $needle)) == $needle;
    }
    public static function encode($data)
    {
        if(self::$encoding == 'json')
        {
            return Zend_Json::encode($data);
        }
        return base64_encode(serialize($data));
    }
    public static function decode($data)
    {
        if(self::$encoding == 'json')
        {
            return Zend_Json::decode($data);
        }
        $data = base64_decode($data);
        $data = unserialize($data);
        if(false === $data)
        {
            throw new Exception('malformed data to decode');
        }
        return $data;
    }
    public static $escape_method='htmlspecialchars';
    public static $escape_encoding='UTF-8';
    /**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
     * {@link $encoding} setting.
     *
     * @see Zend_View_Abstract
     * @param mixed $var The output to escape.
     * @return mixed The escaped value.
     */
    public static function escape($var)
    {
        if (in_array(self::$escape_method, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func(self::$escape_method, $var, ENT_COMPAT, self::$escape_encoding);
        }

        if (1 == func_num_args())
        {
            return call_user_func(self::$escape_method, $var);
        }
        $args = func_get_args();
        return call_user_func_array(self::$escape_method, $args);
    }
    public static function loadCSV($csv_file, $first_row_as_header=true)
    {
        $rows = array();
        if($handle = @fopen($csv_file, "r"))
        {
            $headers= array();
            while ($row = fgetcsv($handle, 4096, ","))
            {
                if($first_row_as_header && empty($headers))
                {
                    $headers = $row;
                }
                else
                {
                    if($first_row_as_header)
                    {
                        $rows []= array_combine($headers, $row);
                    }
                    else
                    {
                        $rows []= $row;
                    }
                }
            }
            fclose($handle);
        }
        return $rows;
    }
    /**
     * 将数据导出为 Excel
     *
     * 使用 PHPExcel 库将数据转换成 PHPExcel 格式的文档并保存
     *
     *
     * @param string $filepath      导出的 Excel 文件名
     * @param string $title         Excel Worksheet 标题
     * @param array $rows           数据记录集，每个记录为一个关联数组
     * @param array $headers        表头
     * @param array $properties     文档属性，可填写的字段有
     * Creator(作者)、LastModifiedBy(修改人)、Title(标题)、Subject(主题)、
     * Description(描述)、Keywords(关键字)、Category(分类)等
     */
    public static function exportExcel($filepath, $title, $rows, $headers, $properties=array())
    {
        require_once('phpexcel/PHPExcel.php');
        $PHPExcel = new PHPExcel();
        $PHPExcel->getProperties()
        ->setCreator(isset($properties['Creator'])?$properties['Creator']:'Capitalvue')
        ->setLastModifiedBy(isset($properties['LastModifiedBy'])?$properties['LastModifiedBy']:'Capitalvue')
        ->setTitle(isset($properties['Title'])?$properties['Title']:'Capitalvue ')
        ->setSubject(isset($properties['Subject'])?$properties['Subject']:'Office 2007 XLSX Test Document')
        ->setDescription(isset($properties['Description'])?$properties['Description']:'Capitalvue Report')
        ->setKeywords(isset($properties['Keywords'])?$properties['Keywords']:'Capitalvue,Report')
        ->setCategory(isset($properties['Category'])?$properties['Category']:'Capitalvue Report');
        $PHPExcel->setActiveSheetIndex(0);
        $sheet = $PHPExcel->getActiveSheet();
        if (PHPExcel_Shared_String::CountCharacters($title) > 31)
        {
            $title = PHPExcel_Shared_String::Substring($title, 0, 28) . '...';
        }
        $sheet->setTitle($title);
        $i = 0;
        $j = 0;
        $i++;
        foreach ($headers as $header) {
            $sheet->setCellValue($pos=chr(ord('A')+$j++).$i, $header);
        }

        foreach($rows as $row)
        {
            $i++;
            $j=0;
            foreach ($row as $col)
            {
                //$sheet->setCellValue($pos=chr(ord('A')+$j++).$i, strip_tags($col));
                $pos=chr(ord('A')+$j++).$i;
                $sheet->getCell($pos)->setValueExplicit($col, PHPExcel_Cell_DataType::TYPE_STRING);
            }
        }

        PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel5')->save($filepath);
    }
}