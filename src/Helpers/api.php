<?php
/**
 * Created by PhpStorm.
 * User: menq
 * Date: 7/18/17
 * Time: 5:23 PM
 */


function BBaddShortcode($key, $shortcode)
{
    $codes = \Config::get('shortcode.extra',[]);
    array_push($codes, [$key => $shortcode]);
    \Config::set('shortcode.extra', $codes);
}

function BBGetAdminLoginUrl()
{
    $adminLoginPage = Sahakavatar\Modules\Models\AdminPages::where('slug', 'admin-login')->first();
    return $adminLoginPage ? $adminLoginPage->url : '/admin/login';

}