<?php
/**
 * Copyright (c) 2017. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Btybug\btybug\Models;

class BBGiveMe
{
    public static function GiveArray($length = 5)
    {
        $myarray = [];
        for ($i = 0; $i <= $length; $i++) {
            $myarray[] = self::GiveString();
        }
        return $myarray;
    }

    public static function GiveString($len = 10)
    {
        $result = "";
        $chars = "abcdefghijklmnopqrstuvwxyz";
        $charArray = str_split($chars);
        for ($i = 0; $i < $len; $i++) {
            $randItem = array_rand($charArray);
            $result .= "" . $charArray[$randItem];
        }
        return ucfirst($result);
    }

    public static function GiveNumber($min = null, $max = null)
    {
        return mt_rand($min, $max);
    }


}