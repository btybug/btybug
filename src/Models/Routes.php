<?php
/**
 * Created by PhpStorm.
 * User: Comp1
 * Date: 1/20/2017
 * Time: 11:01 AM
 */

namespace Btybug\btybug\Models;

use Btybug\Uploads\Repository\Plugins;
use Btybug\Console\Models\AdminPages;
use Btybug\Console\Repository\AdminPagesRepository;

/**
 * Class Routes
 * @package Btybug\btybug\Models
 */
class Routes
{
    public $array;

    public static function getModuleRoutes($method, $sub)
    {
        $routes = array();
        $new_array = [];
        $routeCollection = \Route::getRoutes();
        foreach ($routeCollection as $value) {
            $routes[$value->methods()[0]][$value->uri()] = [];
            if (!isset($routes[$value->methods()[0]][$value->getPrefix()])) {
                $routes[$value->methods()[0]][$value->getPrefix()] = [];
            }
        }
        foreach ($routes[$method] as $key => $val) {
            if (isset($key[0]) && $key[0] == '/') {
                $key = substr($key, 1);
            }
            $routes[$method][$key] = $val;
//            if (isset($routes[$method]['/' . $key])) {
//                unset($routes['GET']['/' . $key]);
//            }

        }
        if (!isset($routes[$method]['admin'])) {
            $routes[$method]['admin'] = [];
        }
        ksort($routes[$method]);
        $routes[$method] = (self::keysort($routes[$method], $sub));
        $_this = new static();
        if(isset($routes[$method][$sub]))
            $_this->array = collect($routes[$method][$sub]);

        return $_this;
    }

    public static function keysort($array, $url, $count = 0)
    {
        foreach ($array as $key => $value) {
            $count++;
            if (self::is_child($url, $key)) {
                $array[$url][$key] = [];
                unset($array[$key]);
            }
        }
        if (isset($array[$url]) && count($array[$url])) {
            foreach ($array[$url] as $k => $v) {
                $array[$url] = self::keysort($array[$url], $k);
            }
        }
        return $array;
    }

    public static function is_child($parent, $child)
    {
        if ($parent == $child) return false;
        $parent = self::clean_urls($parent);
        $child = self::clean_urls($child);
        return (self::array_sort_with_count($child, count($parent)) == $parent);
    }

    public static function clean_urls($url)
    {
        if (isset($url[0]) && $url[0] == '/') {
            $url = substr($url, 1);
        }
        return explode('/', $url);
    }

    public static function array_sort_with_count(array $array, $count)
    {
        $cunk = array_chunk($array, $count);
        if (count($cunk)) {
            return $cunk[0];
        }
        return false;
    }

    public static function optimizePages()
    {
        $pageRepo = new AdminPagesRepository();
        //Get all routes
        $routes = self::getRoutes()->getRoutesByMethod();
        //Get only routes by GET method
        $routeCollection = $routes['GET'];
        foreach ($routeCollection as $key => $val) {
            //check if route is admin related
            if (str_contains($val->uri, "admin")) {
                //check if route starts with / or no
                if (starts_with($val->uri, "/")) {
                    $withSlash = $val->uri;
                    $withoutSlash = ltrim($val->uri, '/');
                } else {
                    $withoutSlash = $val->uri;
                    $withSlash = "/" . $val->uri;
                }

                // get admin pages which have that route url
                $page = $pageRepo->getByUrl($withoutSlash);//AdminPages::where('url', $withSlash)->orWhere('url', $withoutSlash)->first();

                //if no page with url, create page
                if (!$page) {
                    $data = explode('/', $withoutSlash);
                    if (count($data) == 2) {
                        $parent = $pageRepo->getByUrl('admin');
//                        AdminPages::create([
//                            'module_id' => '',
//                            'title' => $data[1],
//                            'url' => $withoutSlash,
//                            'slug' => uniqid(),
//                            'parent_id' => $parent->id,
//                            'is_default' => 0,
//                        ]);
                    } else {
                        $data = explode('/', $withoutSlash);
                        $count = count($data);

//                        AdminPages::create([
//                            'module_id' => '',
//                            'title' => last($count),
//                            'url' => $withoutSlash,
//                            'slug' => uniqid(),
//                            'parent_id' => $parent->id,
//                            'is_default' => 0,
//                        ]);
                        dd($withoutSlash, $count, 'mej@');
                    }


//                    dd($withoutSlash,$parent,$data,'mej@');
                }
//                dd($withSlash,$withoutSlash,$page);
            }
//            $page = AdminPages::where('url',$key)->first();
//            dd($page,$key);
        }
        dd($routeCollection, 'verj');
    }

    public static function getRoutes()
    {
        return Route::getRegisteredRoutes();
    }

    public static function registerPages($slug)
    {

        $package = new Plugins();
        $adminPagesRepo = new AdminPagesRepository();
        $package->plugins();
        $module = $package->find($slug);
        if (!$module) {
            $package->modules();
            $module = $package->find($slug);
        }
        if(! $module) {
            $package->appPlugins();
            $module = $package->find($slug);
        }

        if ($module) {
            if ($module->type == 'app-plugin' || $module->type == 'plugin' || $module->type == 'module' || $module->type == 'addon' || $module->type=='package') {
                $url = strtolower('admin/' . $module->route);
            } else {
                return false;
            }
            $routes = self::getRoutesStratWith($url, "GET");
            $message = [];

//            $activeLayout = ContentLayouts::active()->activeVariation();
            if (count($routes)) {
                foreach ($routes as $key => $value) {
                    if ($value[1] !== false) {
                        if (!$value[0]) {
                            $parent_id = 0;
                            $pr_url = substr($value[1], 0, strrpos($value[1], "/"));
                            if ($pr_url != 'admin' && $pr_url != '/admin') {
                                $parent = $adminPagesRepo->getByUrl($pr_url);
                                if ($parent) $parent_id = $parent->id;
                            }

                            $page = $adminPagesRepo->findBy('url', '/' . $key);
                            if (!$page) {
                                $exp = explode('/', $key);
                                $title = end($exp);
                                if (end($exp) == '{param}') {
                                    $count = count($exp);
                                    if ($count > 2) $title = $exp[$count - 2];
                                }
                                BBRegisterAdminPages($slug, $title, '/' . $key, null, $parent_id);
                                $message['success'][$key] = end($exp);
                            } else {
                                $message['exist'][$key] = 'registred as ' . $page->title;
                            }
                        } else {
                            $page = $adminPagesRepo->findBy('url', '/' . $key);
                            if (!$page) {
                                $parent = $adminPagesRepo->getByUrl($value[1]);
                                if ($parent) {
                                    $exp = explode('/', $key);
                                    $title = end($exp);
                                    if (end($exp) == '{param}') {
                                        $count = count($exp);
                                        if ($count > 2) $title = $exp[$count - 2];
                                    }
                                    BBRegisterAdminPages($slug, $title, '/' . $key, null, $parent->id);
                                    $message['success'][$key] = end($exp);
                                } else {
                                    $message['warning'][$key] = 'invalid url created not following rules!!!';
                                }
                            } else {
                                $message['exist'][$key] = 'registred as ' . $page->title;
                            }
                        }
                    } else {
                        $message['warning'][$key] = 'invalid url created not following rules!!!';
                    }
                }
            }

            return $message;
        }
        return false;
    }

    public static function getRoutesStratWith($start, $method)
    {
        $routes = array();
        $routeCollection = self::getRoutes();
        foreach ($routeCollection as $value) {
            if (strpos($value->uri, $start) === 0) {
                $routes[$value->uri] = $value->getPrefix();
                $routes[$value->methods[0]][BBmakeUrlPermission($value->uri, '/')] = ($value->getPrefix() == $value->uri || $value->getPrefix() == ('/' . $value->uri)) ? [0, BBmakeUrlPermission($value->getPrefix(), '/')] : [1, BBmakeUrlPermission($value->getPrefix(), '/')];
            }

        }
        return (isset($routes[$method])) ? $routes[$method] : $routes;
    }

    public static function test()
    {
        $test = AdminPages::find(4)->children;
        dd($test);
    }

    public function html()
    {
        $array = $this->array;
        if($array)
        return $this->keysort_html($array->toArray()).'</ul>';
    }

    protected function keysort_html($array, $count = 0, $url = null)
    {
        $html = '';
        if (!$url) {
            $html .= '<ul>';
        }
        if ($count < count($array)) {
            $count++;
            if (!$url) {
                $keys = array_keys($array);
                $url = $keys[0];
            } else {
                $keys = array_keys($array);
                $be = array_search($url, $keys);
                $url = $keys[$be + 1];
            }

            if (count($array[$url])) {
                $dropmenu = 'true';
            } else {
                $dropmenu = 'false';
            }

            $html .= '<li>';
            $html .= $url;
//            $html .= '</li>';
            if (count($array[$url])) {
                $html .= $this->keysort_html($array[$url]);
                $html .= '</ul>';
            }
            $html .= '</li>';
            $html .= $this->keysort_html($array, $count, $url);
        }
        return $html;
    }

}