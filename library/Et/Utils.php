<?php
class Et_Utils
{
    static $base_url;

    static $public_url;

    static $absolute_url;

    public static function absolute_url($url = '')
    {
        if(!self::$absolute_url){
            $serverUrl = new Zend_View_Helper_ServerUrl();
            self::$absolute_url = $serverUrl->serverUrl();
        }
        return self::$absolute_url . ($url ? ('/' . ltrim($url, '/')) : '');
    }

    public static function base_url($url = '', $absolute = false)
    {
        if(!self::$base_url){
            //$request = Zend_Controller_Front::getInstance()->getRequest();
            $request = new Zend_Controller_Request_Http();
            self::$base_url =  $request->getBaseUrl();
        }
        return ($absolute ? self::absolute_url(self::$base_url) : self::$base_url) . '/' . ltrim($url, '/');
    }

    public static function public_url($url = '', $absolute = true)
    {
        if(!self::$public_url){
            //$request = Zend_Controller_Front::getInstance()->getRequest();
            $request = new Zend_Controller_Request_Http();
            self::$public_url = $request->getBasePath();
        }
        return ($absolute ? self::absolute_url(self::$public_url) : self::$public_url) . '/' . ltrim($url, '/');
    }
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
    public static function now($short=false)
    {
        return date($short?'Y-m-d':'Y-m-d H:i:s');
    }
    /**
     * 截取字符串的一段
     *
     * @param string $input
     * @param int $length
     * @param string $suffix
     * @return string
     */
    public static function truncate ($input, $length, $suffix = '...')
    {
        if (strlen($input) <= $length)
        {
            return $length;
        }
        $i = 0;
        $sb = array();
        while ($i < $length - strlen($suffix))
        {
            $ord = ord(substr($input, $i, 1));
            $real_char_length = 1;
            if ($ord >= 224)
            {
                $real_char_length = 3;
            }
            elseif ($ord >= 192)
            {
                $real_char_length = 2;
            }
            else
            {
                $real_char_length = 1;
            }
            $sb[] = substr($input, $i, $real_char_length);
            $i += $real_char_length;
        }
        return implode('', $sb) . $suffix;
    }
    public static function filesize2bytes ($str)
    {
        $bytes = 0;
        $bytes_array = array('B' => 1, 'KB' => 1024, 'MB' => 1024 * 1024,
        'GB' => 1024 * 1024 * 1024, 'TB' => 1024 * 1024 * 1024 * 1024,
        'PB' => 1024 * 1024 * 1024 * 1024 * 1024);
        $bytes = floatval($str);
        if (preg_match('#([KMGTP]?B)$#si', $str, $matches) && ! empty($bytes_array[$matches[1]])) {
            $bytes *= $bytes_array[$matches[1]];
        }
        $bytes = intval(round($bytes, 2));
        return $bytes;
    }
    public static function bytes2filesize ($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . '' . $units[$pow];
    }
    /**
     * 确保目录存在，不存在则创建它
     *
     * @param string $dirName
     * @param string $mode
     * @return bool
     */
    public static function assureDir ($dirName, $mode = 0777)
    {
        is_dir($dirName) || mkdir($dirName, $mode, true);
        return true;
    }
    /**
     * 递归删除目录
     *
     * @param string $dirName
     * @return boolean
     */
    public static function removeDir ($dirName)
    {
        if (! is_dir($dirName))
        {
            return false;
        }
        $handle = @opendir($dirName);
        while (($file = @readdir($handle)) !== false)
        {
            if ($file != '.' && $file != '..')
            {
                $dir = $dirName . '/' . $file;
                is_dir($dir) ? self::removeDir($dir) : @unlink($dir);
            }
        }
        closedir($handle);
        return rmdir($dirName);
    }
    /**
     * 判断字符串是否以另一字符串结尾
     *
     * @param string $s
     * @param string $suffix
     * @return boolean
     */
    public static function endswith ($s, $suffix)
    {
        $pos = strrpos($s, $suffix);
        return ($pos !== false) && (substr($s, $pos) == $suffix);
    }
    /**
     * 判断字符串是否以另一字符串开头
     *
     * @param string $s
     * @param string $prefix
     * @return boolean
     */
    public static function startswith ($s, $prefix)
    {
        return strpos($s, $prefix) === 0;
    }
    /**
     * 字符串转换为 url 样式，也就是
     *
     * 1、 _ 替换为 -
     * 2、 大写字母替换为 - 和对应的小写字母
     *
     * @param string $s
     * @return string
     */
    public static function urlize ($s)
    {
        $s = str_replace('_', '-', $s);
        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '-$1', $s));
    }
    /**
     * 转换为对象样式，也就是
     * 1、全部为小写字母
     * 2、 - 替换为 _
     *
     * @param string $s
     * @return string
     */
    public static function objectize ($s)
    {
        return str_replace('-', '_', self::urlize($s));
    }
    /**
     * 字符串转换为 类 命名样式，也就是
     *
     * 1、 - 加小写字母替换为对应的大写字母
     * 2、 如果要求，可保留 -
     * @param string $s
     * @return string
     */
    public static function classize ($s, $preserve_dash = false)
    {
        $s = ucwords(str_replace(array('-', '_'), ' ', self::urlize($s)));
        return str_replace(' ', $preserve_dash ? '-' : '', $s);
    }
    /**
     * 转换为比较方便阅读的字符串
     *
     * @param string $s
     * @param string $type 可以是 string 字符串， filesize 文件大小等等
     * @return string
     */
    public static function humanize ($s, $type = 'STRING')
    {
        return ucwords(str_replace(array('-', '_'), ' ', self::urlize($s)));
    }
    /**
     * 将列表分割为数组，可选分隔符
     *
     * @param string $list
     * @param string $sep
     * @return array
     */
    public static function list2array ($list, $sep = ',')
    {
        $list = explode($sep, $list);
        $vs = array();
        foreach ($list as $select)
        {
            $vs[] = intval(trim($select));
        }
        return $vs;
    }
    /**
     * 获得客户端IP
     *
     * @param boolean $checkProxy 是否检测代理服务器
     * @return string
     */
    public static function clientIP ($checkProxy = true)
    {
        if ($checkProxy && ! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else
            if ($checkProxy && ! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }
        return $ip;
    }
}