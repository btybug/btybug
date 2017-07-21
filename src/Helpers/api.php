<?php
/**
 * Created by PhpStorm.
 * User: menq
 * Date: 7/18/17
 * Time: 5:23 PM
 */


function BBaddShortcode($key, $shortcode)
{
    $codes = \Config::get('shortcode.extra', []);
    array_push($codes, [$key => $shortcode]);
    \Config::set('shortcode.extra', $codes);
}

function BBGetAdminLoginUrl()
{
    $adminLoginPage = Sahakavatar\Modules\Models\AdminPages::where('slug', 'admin-login')->first();
    return $adminLoginPage ? $adminLoginPage->url : '/admin/login';

}


function BBCheckMemberAccessEnabled()
{
    $reg = BBCheckRegistrationEnabled();
    if ($reg) {
        $settings = \Sahakavatar\Settings\Models\Settings::where('settingkey', 'enable_member_access')->first();
        if ($settings) {
            return ($settings->val == "1");
        }
    }
    return false;
}

function BBCheckRegistrationEnabled()
{
    $settings = \Sahakavatar\Settings\Models\Settings::where('settingkey', 'enable_registration')->first();
    if ($settings) {
        return ($settings->val) ? true : false;
    }
    return false;
}


function BBheader()
{
    $tpl = \Sahakavatar\Settings\Models\Settings::where('section', 'setting_system')->where('settingkey', 'header_tpl')->first();
    if ($tpl and !empty($tpl->val)) {
        return BBRenderTpl($tpl->val);
    }
}

function BBRenderTpl($variation_id, $on_empty = null)
{
    $slug = explode('.', $variation_id);
    if (isset($slug[0]) && isset($slug[1])) {
        $widget_id = $slug[0];
        $variationID = $slug[1];

        $widget = \Sahakavatar\Cms\Models\Templates\Templates::find($widget_id);
        if (!is_null($widget)) {
            $variation = $widget->findVariation($variation_id);
            if (!is_null($variation)) {
                $section = '';//$widget->section();
                $settings = $variation->settings;
                if ($widget->have_settings && !$settings) {
                    return 'Settings are empty';
                }
                return $widget->render(compact(['variation', 'section', 'settings']));
            }
        }

        return 'Wrong widget';
    }

    return $on_empty;
}

function BBRenderPageSections($variation_id, $source = [], $main_view = null)
{

    $slug = explode('.', $variation_id);

    if (isset($slug[0]) && isset($slug[1])) {
        $content_layout = $slug[0];
        $section = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::find($content_layout);


        if (!is_null($section)) {
            $variation = $section->findVariation($variation_id);
            if (!is_null($variation)) {

                $settings = $variation->toArray();
//                if ($section->have_settings && !$settings) {
//                    $settings = [];
//                }

//                $liveSettings = array_except($source, ['pl', 'pl_live_settings', 'page_id']);
                $liveSettings = $source;

                if (count($liveSettings) && is_array($liveSettings) && is_array($settings)) {
                    array_filter($settings, function ($value) {
                        return $value !== '';
                    });
                    array_filter($liveSettings, function ($value) {
                        return $value !== '';
                    });
                    $settings = array_merge($liveSettings, $settings);
                }
                $settings['main_view'] = $main_view;

                return $section->render($settings);
            }
        }

        return false;
    }
}


function BBdiv($action, $key, $html, array $array = [])
{
    $BBarrays = [
        'main_body' => 'BBRenderMainView',
        'sections' => 'BBRenderSections'

    ];
    $bbRenderFunction = $BBarrays[$action];
    $route = Request::route();
    if ($action == 'main_body' && $route->uri() == "admin/manage/frontend/pages/page-preview/{id}") {
        $param = $route->parameter('id');
        $page = \Sahakavatar\Manage\Models\FrontendPage::find($param);
        if ($page) {
            if ($page->type != "custom" && $page->type != "tags")
                return false;
        }
    }

    $atributes = ' ';
    $value = '';
    $array['class'] = $array['class'] . " BBdivs";
    if (count($array)) {
        foreach ($array as $k => $v) {
            if ($k != 'model') {
                $atributes .= "$k=\"$v\"";
            }

        }
    }
    if (isset($array['model'])) {
        $model = $array['model'];
        if (is_string($model)) {
            $value = $model;
        } else {
            if (is_object($model)) {
                $model = $model->toArray();
            }

            if (isset($model[$key])) {
                $value = $model[$key];
            }
        }

    }
    $array = '';
    if (strpos($key, '[]')) {
        $array = 'data-array="true"';
    }

    $data_key = str_replace('[]', '', $key);
    $renderedUnit = $bbRenderFunction($value);

    return '<div data-action=' . $action . ' data-key="' . $data_key . '" ' . $atributes . ' >' . (($renderedUnit) ? $renderedUnit : $html) . '</div><input class="bb-button-realted-hidden-input" type="hidden" ' . $array . ' value="' . $value . '" data-name="' . $data_key . '" name="' . $key . '">';
}

function BBRenderSections($variation_id, $source = [])
{
    if (is_array($variation_id)) {
        $variation_id = $variation_id['id'];
    }
    $slug = explode('.', $variation_id);
    if (isset($slug[0]) && isset($slug[1])) {
        $section_id = $slug[0];
        $section = \Sahakavatar\Cms\Models\Templates\Sections::find($section_id);
        if (!is_null($section)) {
            $variation = $section->findVariation($variation_id);
            if (!is_null($variation)) {
                $settings = $variation->settings;
                if ($section->have_settings && !$settings) {
                    $settings = [];
                }

                return $section->render(compact(['variation', 'settings', 'source']));
            }
        }

        return false;
    }
}

function BBfooter()
{
    $tpl = \Sahakavatar\Settings\Models\Settings::where('section', 'setting_system')->where('settingkey', 'footer_tpl')->first();
    if ($tpl and !empty($tpl->val)) {
        return BBRenderTpl($tpl->val);
    }
}

function BBRenderBackTpl($variation_id, $on_empty = null)
{
    $slug = explode('.', $variation_id);
    if (isset($slug[0]) && isset($slug[1])) {
        $widget_id = $slug[0];
        $variationID = $slug[1];

        $widget = \Sahakavatar\Cms\Models\Templates\BackendTpl::find($widget_id);
        if (!is_null($widget)) {
            $variation = $widget->findVariation($variation_id);
            if (!is_null($variation)) {
                $section = '';//$widget->section();
                $settings = $variation->settings;
                if ($widget->have_settings && !$settings) {
                    return 'Settings are empty';
                }
                return $widget->render(compact(['variation', 'section', 'settings']));
            }
        }

        return 'Wrong widget';
    }

    return $on_empty;
}

function BBleftBar()
{
    $tpl = \Sahakavatar\Settings\Models\Settings::where('section', 'setting_system')->where('settingkey', 'backend_left_bar')->first();
    if ($tpl and !empty($tpl->val)) {
        return BBRenderBackTpl($tpl->val);
    }
}

function BBheaderBack()
{
    $tpl = \Sahakavatar\Settings\Models\Settings::where('section', 'setting_system')->where('settingkey', 'backend_header')->first();
    if ($tpl and !empty($tpl->val)) {
        return BBRenderBackTpl($tpl->val);
    }
}

function BBgetPageLayout()
{
    $route = \Request::route();

    if ($route) {
        if (isset($_GET['pl_live_settings']) && $_GET['pl_live_settings'] == 'page_live') {
            $layoutID = $_GET['pl'];
            $layout = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::findVariation($layoutID);
            if (!$layout) return \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::defaultPageSection();
            $data = explode('.', $layoutID);
            $layout = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::find($data[0]);
            return 'ContentLayouts.' . $layout->folder . '.' . $layout->layout;
        }
    }
    $page = \Sahakavatar\Modules\Models\AdminPages::getPageByURL();

    if (!$page or !$page->layout_id) return \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::defaultPageSection();

    $layout = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::findVariation($page->layout_id);
    if ($layout) {
        $data = explode('.', $page->layout_id);
        $layout = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::find($data[0]);
        return 'ContentLayouts.' . $layout->folder . '.' . $layout->layout;
    }
}

function BBgetPageLayoutSettings()
{
    $page = \Sahakavatar\Modules\Models\AdminPages::getPageByURL();
    if (isset($_GET['pl_live_settings']) && $_GET['pl_live_settings'] == 'page_live') {
        $data = $_GET;
        $live_page = \Sahakavatar\Modules\Models\AdminPages::find($data['page_id']);
        if ($live_page) {
            if ($live_page->settings && isset($_GET['variation'])) {
                $page_settings = json_decode($live_page->settings, true);
                if (!empty($page_settings)) $data = array_merge($page_settings, $data);
            } else {
                $layout = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::findVariation($data['pl']);
                if ($layout) $data = array_merge($layout->settings, $data);
            }
        } else {
            $layout = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::findVariation($data['pl']);
            if ($layout) $data = array_merge($layout->settings, $data);
        }
        return $data;
    }

    if ($page) {
        if ($page->settings) {
            $settings = @json_decode($page->settings, true);
            $layout = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::findVariation($page->layout_id);
            if ($layout) {
                $mainSettings = $settings ? array_merge($settings, $layout->settings) : $layout->settings;
                $json = '<input type="hidden" id="page_layout_settings_json" data-json=' . json_encode($mainSettings, true) . '>';
                echo $json;
                return $mainSettings;
            }
        } else {
            $layout = \Sahakavatar\Cms\Models\ContentLayouts\ContentLayouts::findVariation($page->layout_id);
            $settings = $layout->settings;
            $json = '<input type="hidden" id="page_layout_settings_json" data-json=' . json_encode($layout->settings, true) . '>';
            echo $json;
            return $settings;
        }
    }

    return ['options' => [], 'json' => json_encode([], true)];

}

//TODO Transver in Hooks package
function BBscriptsHook()
{
    $codes = \Config::get('scripts', []);
    $scripts = '';
    foreach ($codes as $key => $value) {
        $scripts .= HTML::script($value);
    }
    return $scripts;
}

//TODO Transver in Framework api.php
function BBFrameworkJs()
{
    return \Sahakavatar\Framework\Models\Framework::activeJs();
}

//TODO transer in user package
if (!function_exists('BBGetUserAvatar')) {
    /**
     * @param null $id
     * @return mixed|null|string
     */
    function BBGetUserAvatar($id = null)
    {
        if ($id) {
            if ($user = \App\User::find($id)) {
                return ($user->profile->avatar) ? url($user->profile->avatar) : '/resources/assets/images/avatar.png';
            }
        } else {
            if (Auth::check()) {
                return (Auth::user()->profile->avatar) ? url(Auth::user()->profile->avatar) : '/resources/assets/images/avatar.png';
            }
        }

        return null;
    }
}

if (!function_exists('BBGetUserName')) {
    /**
     * @param null $id
     * @return mixed|null|string
     */
    function BBGetUserName($id = null)
    {
        if ($id) {
            if ($user = \App\User::find($id)) {
                if (!isset($user->profile)) {
                    return $user->username;
                }

                return ($user->profile->first_name || $user->profile->last_name) ?
                    $user->profile->first_name . ' ' . $user->profile->last_name : $user->username;
            }
        } else {
            if (Auth::check()) {
                if (!isset(Auth::user()->profile)) {
                    return Auth::user()->username;
                }

                return (Auth::user()->profile->first_name || Auth::user()->profile->last_name) ? Auth::user(
                    )->profile->first_name . ' ' . Auth::user()->profile->last_name : Auth::user()->username;
            }
        }

        return null;
    }
}

function BBAdminMenu()
{

    // Get json file
    $menu_array = \Config::get('admin_menus');
    if (!$menu_array) {
        $menu_json_file = file_get_contents(base_path('resources/menus/admin/1.json'));
        $menu_array = json_decode($menu_json_file, true);
    }
    $menu = BBAdminMenuWalker($menu_array);

//    // Optional Menu 1
//    if (BBGetAdminSetting('active-menu-1')) {
//        $file = 'resources/menus/admin/' . BBGetAdminSetting('active-menu-1') . '.json';
//        if (is_file($file)) {
//            $menu .= BBAdminMenuWalker(json_decode(file_get_contents($file), true));
//        }
//    }

    return $menu;
}
function BBAdminMenuWalker($menu_array)
{

    $menu = '';
    if (is_array($menu_array)) {
        $menu = '<ul id="menu-content" class="menu-content collapse out">';

        foreach ($menu_array as $key => $item) {
            $link = 'javascript:void(0)';
            $icon = (isset($item['icon'])) ? $item['icon'] : 'fa fa-share-square-o';
            if (isset($item['custom-link'])) {
                $link = $item['custom-link'];
            }

            $menu .=
                '<li  data-toggle="collapse" data-target="#id' . $key . '" class="collapsed">' .
                '<a href="javascript:void(0)"><i class="' . $icon . '"></i>' . $item['title'];
            if (isset($item['children']) and is_array($item['children'])) {
                $menu .= '<span class="pull-right arrow fa fa-arrow-left"></span>';
            }
            $menu .= '</a>';

            $menu .= '</li>';
            $menu .= '</ul>';
            if (isset($item['children']) and is_array($item['children'])) {
                $menu .= '   <ul class="sub-menu collapse" id="id' . $key . '">';
                foreach ($item['children'] as $child) {
                    $menu .=
                        ' <li class="clearfix">' .
                        ' <a href="' . $child['custom-link'] . '">' . $child['title'] .
                        ' </a>' .
                        ' </li>';
                }
                $menu .= '   </ul>';
            }

        }

    }


    return $menu;
}

function BBgetSiteLogo()
{
    $logo = \Sahakavatar\Settings\Models\Settings::where('section', 'setting_system')->where('settingkey', 'site_logo')->first();
    if (!$logo) return '';

    return url('/resources/assets/images/logo/', $logo->val);
}

function BBgetSiteName()
{
    $name = \Sahakavatar\Settings\Models\Settings::where('section', 'setting_system')->where('settingkey', 'site_name')->first();

    return $name->val;
}

function BBRenderUnits($variation_id, $source = [], $data = NULL)
{
    $field = null;
    $cheked = null;
    $slug = explode('.', $variation_id);
    if (isset($slug[0]) && isset($slug[1])) {
        $widget_id = $slug[0];
        $variationID = $slug[1];
        $unit = \Sahakavatar\Cms\Models\Templates\Units::find($widget_id);
        if (!is_null($unit)) {
            $variation = $unit->findVariation($variation_id);
            if (!is_null($variation)) {
                $settings = $variation->settings;
                if ($unit->have_settings && !$settings) {
                    $settings = [];
                }

                if (isset($source['field'])) {
                    if (is_string($source['field'])) {
                        $field = $source;
                    } else {
                        $field = $source['field'];
                    }
                }

                return $unit->render(compact(['variation', 'settings', 'source', 'field', 'cheked', 'data']));
            }
        }

        return 'Wrong Unit';
    }
}
//TODO transver in Avatar
function plugins_path($path = null)
{
    return rtrim(base_path(config('avatar.plugins.path').DS. $path), '/');
}