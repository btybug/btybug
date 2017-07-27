<?php
/**
 * Created by PhpStorm.
 * User: Arakelyan
 * Date: 7/24/2017
 * Time: 15:13
 */

namespace Sahakavatar\Cms\Services;


use Sahakavatar\Console\Repository\AdminPagesRepository;
use Sahakavatar\User\Repository\RoleRepository;

class RenderService extends GeneralService
{

    private static $segment_array = [];

    public static function getPageByURL()
    {
        $adminPageRepo = new AdminPagesRepository();
        $url = \Request::route()->uri();
        $urlWithoutAdmin = $route = substr($url, 6);

        $page = $adminPageRepo->model()->where('url', $url)->orWhere(function ($query) use ($url, $urlWithoutAdmin) {
            $query->where('url', $urlWithoutAdmin)
                ->orWhere(function ($query) use ($url, $urlWithoutAdmin) {
                    $paramsUrl = self::replaceParametrs();
                    $query->where('url', "/" . $url)
                        ->orWhere('url', $paramsUrl);
                });
        })->first();

        return $page;
    }

    public static function replaceParametrs()
    {
        $segments = \Request::segments();
        self::$segment_array = $segments;
        $params = \Request::route()->parameters();
        if (count($params)) {
            $array = array_where($segments, function ($key, $value) use ($params) {
                if (in_array($value, $params)) {
                    self::$segment_array[$key] = '{param}';
                }
            });
        }

        return implode('/', self::$segment_array);
    }

    public static function checkAccess($page_id, $role_slug)
    {
        $roles = new RoleRepository();
        $pageRepo = new AdminPagesRepository();
        if ($role_slug == 'superadmin') return true;

        $page = $pageRepo->find($page_id);
        $role = $roles->findBy('slug', $role_slug);
        if ($page && $role) {
            $access = $pageRepo->getPermissionsByRole($role);
            if ($access) return true;
        }

        return false;
    }
}