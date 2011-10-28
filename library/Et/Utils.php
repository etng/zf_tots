<?php
class Et_Utils
{
    public static $encoding = 'base64';
    function randItem($item)
    {
        $items = func_get_args();
        return $items[array_rand($items)];
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
    /**
     * 判断字符串是否以特定字符串结尾
     * 
     * @param string $haystack	长字符串
     * @param string $needle	短字符串
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

        if (1 == func_num_args()) {
            return call_user_func(self::$escape_method, $var);
        }
        $args = func_get_args();
        return call_user_func_array(self::$escape_method, $args);
    }    
}