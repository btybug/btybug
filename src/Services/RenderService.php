<?php
/**
 * Created by PhpStorm.
 * User: Arakelyan
 * Date: 7/24/2017
 * Time: 15:13
 */

namespace Btybug\btybug\Services;


use Btybug\Console\Repository\AdminPagesRepository;
use Btybug\Console\Repository\FrontPagesRepository;
use Btybug\User\Repository\RoleRepository;

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
                        ->orWhere(function ($query) use ($paramsUrl) {
                            $query->where('url', "/" . $paramsUrl)
                                ->orWhere('url', $paramsUrl);
                        });
                });
        })->first();

        return $page;
    }

    public static function replaceParametrs()
    {
        $url = \Request::route()->uri;
        $segments = explode('/', $url);
        $params = \Request::route()->parameterNames();

        if (count($params)) {
            $array = array_where($segments, function ($value, $key) use ($params) {
                if (str_contains($value, '{')) {
                    $value = str_replace('{', '', str_replace('}', '', $value));
                    if (in_array($value, $params)) {
                        self::$segment_array[$key] = '{param}';
                    }
                } else {
                    self::$segment_array[$key] = $value;
                }
            });
        } else {
            self::$segment_array = $segments;
        }

        return implode('/', self::$segment_array);
    }

    public static function getFrontPageByURL()
    {
        $adminPageRepo = new FrontPagesRepository();
        $url = \Request::route()->uri();
        $page = $adminPageRepo->model()->where('url', $url)
            ->orWhere(function ($query) use ($url) {
                $paramsUrl = self::replaceParametrs();
                $query->where('url', "/" . $url)
                    ->orWhere(function ($query) use ($paramsUrl) {
                        $query->where('url', "/" . $paramsUrl)
                            ->orWhere('url', $paramsUrl);
                    });
            })->first();

        return $page;
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