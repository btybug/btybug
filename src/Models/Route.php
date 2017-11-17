<?php
/**
 * Copyright (c) 2017. All rights Reserved BtyBug TEAM
 */

/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.11.2017
 * Time: 22:01
 */

namespace Btybug\btybug\Models;
use Illuminate\Support\Facades\Route as BtyRoute;

class Route extends BtyRoute
{
    public static $config = [];

    public static function get($uri, $action, $page = false){
        $route = parent::get($uri, $action);
        if($page){
            self::setPage($route);
        }
        return $route;
    }

    public static function any($uri, $action){
        return parent::any($uri, $action);
    }


    public static function setPage($route)
    {
        static::$config[$route->uri()] = $route;
        \config::set('routes',static::$config);
    }

    public static function getRegisteredRoutes ()
    {
        return \config::get('routes');
    }
}