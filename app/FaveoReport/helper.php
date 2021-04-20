<?php

if (!function_exists('random_color_part'))
{
    /**
     * get color parts
     * @return string
     */
    function random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

}
if (!function_exists('random_color'))
{
    /**
     * get random color
     * @return string
     */
    function random_color()
    {
        return "#" . random_color_part() . random_color_part() . random_color_part();
    }

}

