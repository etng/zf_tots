<?php
class Et_Utils
{
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
}