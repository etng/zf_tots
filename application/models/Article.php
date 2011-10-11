<?php
final class Application_Model_Article extends Application_Model_Abstract
{
    public static function Table()
    {
        return new Application_Model_Table_Article();
    }
}
?>