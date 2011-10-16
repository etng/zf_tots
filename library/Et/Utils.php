<?php
class Et_Utils
{
    function randItem($item)
    {
        $items = func_get_args();
        return $items[array_rand($items)];
    }
}