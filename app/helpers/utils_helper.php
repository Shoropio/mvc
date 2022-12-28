<?php

//clean number
if (!function_exists('cleanNumber')) {
    function cleanNumber($num)
    {
        $num = strTrim($num);
        $num = esc($num);
        if (empty($num)) {
            return 0;
        }
        return intval($num);
    }
}
