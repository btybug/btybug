<?php
/**
 * Created by PhpStorm.
 * User: menq
 * Date: 7/18/17
 * Time: 5:23 PM
 */
function module_path($path=''){
    return app()->basePath().(DS.'vendor'.DS.'sahak.avatar').($path ? DS.$path : $path);
}
function BBaddShortcode($key, $function)
{
    $codes = \Config::get('shortcode.extra', []);
    array_push($codes, [$key => $function]);
    \Config::set('shortcode.extra', $codes);
}

function BBGetAdminLoginUrl()
{
    $adminPagesReopsitory = new \Btybug\Console\Repository\AdminPagesRepository();
    $adminLoginPage = $adminPagesReopsitory->findBy('slug', 'admin-login');
    return $adminLoginPage ? $adminLoginPage->url : '/admin/login';

}


function BBCheckMemberAccessEnabled()
{
    $reg = BBCheckRegistrationEnabled();
    if ($reg) {
        $settings = \Btybug\btybug\Models\Settings::where('settingkey', 'enable_member_access')->first();
        if ($settings) {
            return ($settings->val == "1");
        }
    }
    return false;
}

function BBCheckRegistrationEnabled()
{
    $settings = \Btybug\btybug\Models\Settings::where('settingkey', 'enable_registration')->first();
    if ($settings) {
        return ($settings->val) ? true : false;
    }
    return false;
}


function BBheader()
{
    $tpl = \Btybug\btybug\Models\Settings::where('section', 'setting_system')->where('settingkey', 'header_tpl')->first();
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
        $widget = \Btybug\btybug\Models\Templates\Units::find($widget_id);
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
        $section = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::find($content_layout);
        if (!is_null($section)) {
            $variation = $section->findVariation($variation_id);
            if (!is_null($variation)) {
                $settings = $variation->toArray();
                $liveSettings = $source;
                if (count($liveSettings) && is_array($liveSettings) && is_array($settings)) {
                    array_filter($settings, function ($value) {
                        return $value !== '';
                    });
                    array_filter($liveSettings, function ($value) {
                        return $value !== '';
                    });
                    $settings = array_merge($liveSettings, $settings['settings']);
                }
                $settings['main_view'] = $main_view;
                return $section->render($settings);
            }
        }
        return false;
    }
}

function BBRenderPageBody($slug, $data = [], $main_view = null)
{
    $section = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::renderPageBody($slug, $data);
    return $section;
}

function BBdiv($key, $html, array $array = [])
{

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
    $renderedUnit = BBRenderUnits($value);
    return '<div data-action="unit"' . ' data-key="' . $key . '" ' . $atributes . ' >' . (($renderedUnit) ? $renderedUnit : $html) . '</div><input class="bb-button-realted-hidden-input" type="hidden" ' . $array . ' value="' . $value . '" data-name="' . $key . '" name="' . $key . '">';
}

function BBDiv2($key, $tag, $html, $array = [])
{
    $atributes = ' ';
    $value = '';
    $array['class'] = $array['class'] . " BBdivs";
    $array['data-type'] = $tag;
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
    $hiddenName = isset($array['data-name-prefix']) ? $array['data-name-prefix'] . '[' . $key . ']' : $key;
    $array = '';
    if (strpos($key, '[]')) {
        $array = 'data-array="true"';
    }
    $data_key = str_replace('[]', '', $key);

    $renderedUnit = BBRenderUnits($value);
    return '<div data-action="unit"' . ' data-key="' . $data_key . '" ' . $atributes . ' >' . (($renderedUnit) ? $renderedUnit : $html) . '</div><input class="bb-button-realted-hidden-input" type="hidden" ' . $array . ' value="' . $value . '" data-name="' . $key . '" name="' . $key . '">';
}

function BBRenderSections($variation_id, $source = [])
{
    if (is_array($variation_id)) {
        $variation_id = $variation_id['id'];
    }
    $slug = explode('.', $variation_id);
    if (isset($slug[0]) && isset($slug[1])) {
        $section_id = $slug[0];
        $section = \Btybug\btybug\Models\Templates\Sections::find($section_id);
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
    $tpl = \Btybug\btybug\Models\Settings::where('section', 'setting_system')->where('settingkey', 'footer_tpl')->first();
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

        $widget = \Btybug\btybug\Models\Templates\Units::find($widget_id);
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
    $tpl = \Btybug\btybug\Models\Settings::where('section', 'setting_system')->where('settingkey', 'backend_left_bar')->first();
//    dd($tpl);
    if ($tpl and !empty($tpl->val)) {
        return BBRenderBackTpl($tpl->val);
    }
}

function BBheaderBack()
{
    $page = \Btybug\btybug\Services\RenderService::getPageByURL();

    if($page && $page->header){
        $settingsRepo = new \Btybug\btybug\Repositories\AdminsettingRepository();
        $settings = $settingsRepo->findBy('section', 'backend_settings');
        if ($settings && $settings->val) $data = json_decode($settings->val, true);

        if(isset($data['header_unit']))
            return BBRenderUnits($data['header_unit']);
    }
}

function BBfooterBack()
{
    $page = \Btybug\btybug\Services\RenderService::getPageByURL();

    if($page && $page->footer){
        $settingsRepo = new \Btybug\btybug\Repositories\AdminsettingRepository();
        $settings = $settingsRepo->findBy('section', 'backend_settings');
        if ($settings && $settings->val) $data = json_decode($settings->val, true);

        if(isset($data['footer_unit']))
            return BBRenderUnits($data['footer_unit']);
    }
}

function main_content()
{
    $page = \Btybug\btybug\Services\RenderService::getFrontPageByURL();
    if ($page) {
        if ($page->content_type == "editor") {
            echo $page->main_content;
        } else {
            return BBRenderUnits($page->template);
        }
    }
}

function BBgetPageLayout()
{
    $route = \Request::route();
    if ($route) {
        if (isset($_GET['pl_live_settings']) && $_GET['pl_live_settings'] == 'page_live') {
            $layoutID = $_GET['pl'];
            $layout = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::findVariation($layoutID);
            if (!$layout) return \Btybug\btybug\Models\ContentLayouts\ContentLayouts::defaultPageSection();
            $data = explode('.', $layoutID);
            $layout = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::find($data[0]);
            return 'ContentLayouts.' . $layout->folder . '.' . $layout->layout;
        }
    }
    $page = \Btybug\btybug\Services\RenderService::getPageByURL();
    $data = [];
    if ($page->layout_id) {
        $slug = explode('.',$page->layout_id);
        $layout = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::find($slug[0]);
        if ($layout) return 'ContentLayouts.' . $layout->folder . '.' . $layout->layout;
    } else {
        $settingsRepo = new \Btybug\btybug\Repositories\AdminsettingRepository();
        $settings = $settingsRepo->findBy('section', 'backend_settings');
        if ($settings && $settings->val) $data = json_decode($settings->val, true);

        if (isset($data['backend_page_section']) && $data['backend_page_section']) {
            $slug = explode('.', $data['backend_page_section']);
            $layout = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::find($slug[0]);
            if ($layout) return 'ContentLayouts.' . $layout->folder . '.' . $layout->layout;
        }
    }
}

function BBgetPageLayoutSettings()
{
    $page = \Btybug\btybug\Services\RenderService::getPageByURL();
    if (isset($_GET['pl_live_settings']) && $_GET['pl_live_settings'] == 'page_live') {
        $data = $_GET;
        $AdminPagesRepo = new \Btybug\Console\Repository\AdminPagesRepository();
        $live_page = $AdminPagesRepo->find($data['page_id']);
        if ($live_page) {
            if ($live_page->settings && isset($_GET['variation'])) {
                $page_settings = json_decode($live_page->settings, true);
                if (!empty($page_settings)) $data = array_merge($page_settings, $data);
            } else {
                $layout = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::findVariation($data['pl']);
                if ($layout) $data = array_merge($layout->settings, $data);
            }
        } else {
            $layout = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::findVariation($data['pl']);
            if ($layout) $data = array_merge($layout->settings, $data);
        }
        return $data;
    }

    if ($page) {
        $data = [];
        if ($page->settings && $page->layout_id) {
            $data = json_decode($page->settings, true);

            $layout = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::findVariation($page->layout_id);
            if ($layout) {
                $mainSettings = array_merge($data, $layout->settings);
                $json = '<input type="hidden" id="page_layout_settings_json" data-json=' . json_encode($mainSettings, true) . '>';
                echo $json;
                return $mainSettings;
            }
        } else {
            $settingsRepo = new \Btybug\btybug\Repositories\AdminsettingRepository();
            $settings = $settingsRepo->findBy('section', 'backend_settings');
            if ($settings && $settings->val) $data = json_decode($settings->val, true);

            if (isset($data['backend_page_section']) && $data['backend_page_section']) {
                $layout = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::findVariation($data['backend_page_section']);
                if ($layout) {
                    $mainSettings = array_merge($data, $layout->settings);
                    $json = '<input type="hidden" id="page_layout_settings_json" data-json=' . json_encode($mainSettings, true) . '>';
                    echo $json;
                    return $mainSettings;
                }
            }
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
    return \Btybug\Framework\Models\Framework::activeJs();
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

                return (Auth::user()->profile->first_name || Auth::user()->profile->last_name) ? Auth::user()->profile->first_name . ' ' . Auth::user()->profile->last_name : Auth::user()->username;
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
    $settingRepo = new Btybug\btybug\Repositories\AdminsettingRepository();
    $logo = $settingRepo->getSettings('setting_system', 'site_logo');
    if (!$logo) return '';

    return url('public/images/logo', $logo->val);
}

function BBgetSiteName()
{
    $settingRepo = new Btybug\btybug\Repositories\AdminsettingRepository();
    $name = $settingRepo->getSettings('setting_system', 'site_name');
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

        $unit = \Btybug\btybug\Models\Templates\Units::find($widget_id);
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
    return rtrim(base_path(config('avatar.plugins.path') . DS . $path), '/');
}

//TODO transver in Manage
function hierarchyAdminPagesListFull($data, $parent = true, $icon = true, $id = 0)
{
    $output = '';
    // Loop through items
    if ($data) {
        foreach ($data as $item) {
            $children = $item->childs;

            $output .= ' <ol class="pagelisting">';
            $output .= '<li data-id="' . $item->id . '">';
            $output .= '<div class="listinginfo">';
            $output .= '<div class="lsitingbutton">';
            if($item->content_type == "special"){
                $settings = json_decode($item->settings,true);

                $output .= '<a href="' . url($settings['edit_url']) . '" class="btn"><i class="fa fa-cog fa-spin"></i></a>';
            }else{
                $output .= '<a href="' . url('/admin/manage/structure/front-pages/settings', $item->id) . '" class="btn"><i class="fa fa-pencil"></i></a>';
            }

            if ($item->type == 'custom') {
                $output .= '<a href="' . url('/admin/manage/structure/front-pages/new', $item->id) . '" class="btn"><i class="fa fa-plus"></i></a>';
                $output .= '<a data-href="' . url('/admin/manage/structure/front-pages/delete') . '" data-key="' . $item->id . '" data-type="Page ' . $item->title . '"  class="delete-button btn trashBtn"><i class="fa fa-trash"></i></a>';
            }
//        $output .= '<a data-toggle="collapse" data-pagecolid="' . $item->id . '" data-parent="#accordion' . $item->id . '" href="#collapseOne' . $item->id . '" aria-expanded="true" aria-controls="collapseOne" class="link_name collapsed">';
//        $output .= $item->title;
//        $output .= '</a>';
            $output .= '</div>';
            $output .= '<button class="btn btn-collapse" type="button" data-caction="collapse">';
            if (count($children)) {
                $output .= '<i class="fa fa-minus" data-collapse="' . $item->id . '" aria-hidden="true"></i>';
            }
            $output .= '</button>';

            $output .= '<span class="listingtitle">' . $item->title . '</span>';
            $output .= '</div>';
            /* Actions */
            /* Actions END */
            if (count($children)) {
                $output .= hierarchyAdminPagesListFull($children, false, $icon, 0);
            }

//        $output .= '</li>';
            // If this is the top parent
            $output .= '</li>';
            $output .= '</ol>';
        }
    }
    // Return data tree
    return $output;
}

function BBGetTables()
{
    $tables = \DB::select('SHOW TABLES');
    $data = [];
    if (count($tables)) {
        foreach ($tables as $values) {
            foreach ($values as $value) {
                $data[$value] = $value;
            }
        }
    }

    return $data;
}

function BBGetTableColumn($table = null)
{
    $data = [];
    if ($table && \Schema::hasTable($table)) {
        $colums = \DB::select('SHOW COLUMNS FROM ' . $table);
        if (count($colums)) {
            foreach ($colums as $values) {
                $data[array_first($values)] = array_first($values);
            }
        }
    }

    return $data;
}

function BBbutton($action, $key, $text, array $array = [])
{
    $route = Request::route();
    if ($action == 'main_body' && $route->uri() == "admin/manage/structure/front-pages/page-preview/{id}") {
        $param = $route->parameter('id');
        $page = \Btybug\FrontSite\Models\FrontendPage::find($param);
        if ($page) {
            if ($page->type != "custom" && $page->type != "tags")
                return false;
        }
    }

    $atributes = ' ';
    $value = '';
    $array['class'] = $array['class'] . " BBbuttons";
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
    $hiddenName = isset($array['data-name-prefix']) ? $array['data-name-prefix'] . '[' . $key . ']' : $key;
    $array = '';
    if (strpos($key, '[]')) {
        $array = 'data-array="true"';
    }
    $data_key = str_replace('[]', '', $key);
    return '<button type="button" data-action=' . $action . ' data-key="' . $data_key . '" ' . $atributes . ' >' . $text . '</button><input class="bb-button-realted-hidden-input" type="hidden" ' . $array . ' value="' . $value . '" data-name="' . $data_key . '" name="' . $hiddenName . '">';
}

function BBbutton2($type, $key, $tag, $text, $array = [])
{
    $atributes = ' ';
    $value = '';
    $array['class'] = $array['class'] . " BBbuttons";
    $array['data-type'] = $tag;
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
    $hiddenName = isset($array['data-name-prefix']) ? $array['data-name-prefix'] . '[' . $key . ']' : $key;
    $array = '';
    if (strpos($key, '[]')) {
        $array = 'data-array="true"';
    }
    $data_key = str_replace('[]', '', $key);
    $html=View::make('btybug::bbbutton',compact('type','data_key','atributes','text','array','value','hiddenName'))->render();
    return $html;
}

function BBgetDateFormat($date, $format = null)
{
    if (!$date) null;

    if (!is_numeric($date))
        $date = strtotime($date);

    if ($format) {
        return date($format, $date);
    }

    $settings = DB::table('settings')->where('section', 'setting_system')->where(
        'settingkey',
        'date_format'
    )->first();

    if ($settings) {
        if (strpos($settings->val, '%') !== false) {
            return (strftime($settings->val, $date)) ? strftime($settings->val, $date) : date('m/d/Y', $date);
        } else {
            return date($settings->val, $date);
        }
    }

    return date('m/d/Y', $date);
}

/**
 * @param $time
 * @return bool|string
 */
function BBgetTimeFormat($time)
{
    if (!$time) null;

    $settings = \DB::table('settings')->where('section', 'setting_system')->where(
        'settingkey',
        'time_format'
    )->first();

    if ($settings) {
        if ($settings->val == 'seconds') {
            return date("H:i:s", strtotime($time));
        }

        if ($settings->val == '12hrs') {
            // 24-hour time to 12-hour time
            return date("g:i a", strtotime($time));
        }
    }

    return date("H:i", strtotime($time));
}

function BBField($data)
{
    $fieldHtml = '';
    if (isset($data['slug'])) {
        $fieldRepo = new \Btybug\Console\Repository\FieldsRepository();
        $field = $fieldRepo->findBy('slug', $data['slug']);
        if ($field) {
            $field_html = null;
            switch ($field->field_html) {
                case 'default':
                    $defaultFieldHtml = \DB::table('settings')->where('section', 'setting_system')
                        ->where('settingkey', 'default_field_html')->first();
                    $variationId = $defaultFieldHtml->val;
                    $field_html = \Btybug\btybug\Models\Templates\Units::findByVariation($variationId);
                    break;
                case 'custom':
                    $field_html = \Btybug\btybug\Models\Templates\Units::findByVariation($field->custom_html);
                    break;
            }

            if (isset($data['html']) && $data['html'] == 'no') {
                $fieldHtml = BBRenderUnits($field->unit, $field->toArray());
            } else {
                if ($field_html) {
                    $fieldHtml = $field_html->render(['settings' => $field->toArray()]);
                } else {
                    $fieldHtml = BBRenderUnits($field->unit, $field->toArray());
                }
            }

        }
    }
    return $fieldHtml;
}

function BBFieldHidden($data)
{
    $fieldHtml = '';
    if (isset($data['slug'])) {
        $fieldRepo = new \Btybug\Console\Repository\FieldsRepository();
        $field = $fieldRepo->findBy('slug', $data['slug']);
        if ($field) {
            $name = $field->table_name . "_" . $field->column_name;
            $fieldHtml = "<input type='hidden' name='" . $name . "' value='" . $field->default_value . "' />";
        }
    }

    return $fieldHtml;
}

function BBMasterFormsList()
{
    $forms = new \Btybug\Console\Repository\FormsRepository();
    return $forms->getByTypeNewPluck()->toArray();
}

/**
 * @param null $id
 * @return URL|null|string
 */
function BBGetUserCover($id = null)
{
    if ($id) {
        $userRepository = new \Btybug\User\Repository\UserRepository();
        if ($user = $userRepository->find($id)) {
            return ($user->profile->cover) ? url($user->profile->cover) : '/resources/assets/images/profile.jpg';
        }
    } else {
        if (Auth::check()) {
            return (Auth::user()->profile->cover) ? url(Auth::user()->profile->cover) : '/resources/assets/images/profile.jpg';
        }
    }

    return null;
}

//TODO transver in User
function BBGetUserRole($id = null)
{
    if ($id) {
        $userRepository = new \Btybug\User\Repository\UserRepository();
        if ($user = $userRepository->find($id)) {
            return ($user->role->name);
        }
    } else {
        if (Auth::check()) {
            return (Auth::user()->role->name);
        }
    }

    return null;
}

function BBGetAllValidations()
{
    return \Config::get('validations');
}

function issetReturn($array, $item, $default = null)
{

    if (is_array($array)) {
        if (isset($array[$item]) && $array[$item] != "") {
            return $array[$item];
        }
    }

    return $default;
}


/**
 * @param null $id
 * @return mixed|null|string
 */
//TODO transver in USER
function BBGetUser($id = null, $column = 'username')
{
    if ($id) {
        $userRepo = new \Btybug\User\Repository\UserRepository();
        $user = $userRepo->find($id);
        if ($user) {
            if (isset($user->$column)) {
                return $user->$column;
            } elseif (isset($user->profile)) {
                if (isset($user->profile->$column))
                    return $user->profile->$column;
            }
        }
    } else {
        if (Auth::check()) {
            if (isset(Auth::user()->$column)) {
                return Auth::user()->$column;
            } elseif (isset(Auth::user()->profile)) {
                if (isset(Auth::user()->profile->$column))
                    return Auth::user()->profile->$column;
            }
        }
    }

    return null;
}

function BBGetUserAvatar($id = null)
{
    if ($id) {
        $userRepo = new \Btybug\User\Repository\UserRepository();
        $user = $userRepo->find($id);
        if ($user) {
            return ($user->profile->avatar) ? url($user->profile->avatar) : url('public/images/avatar.png');
        }
    } else {
        if (Auth::check()) {
            return (Auth::user()->profile->avatar) ? url(Auth::user()->profile->avatar) : url('public/images/avatar.png');
        }
    }

    return null;
}

//TODO transver in Console
function hierarchyAdminPagesListWithModuleName($data, $moduleCh = null, $icon = true, $roleSlug = null, $checkbox = false)
{
    $plugins = new \Avatar\Avatar\Repositories\Plugins();
    $plugins->modules();
    $modules = $plugins->getPlugins()->toArray();
    $plugins->plugins();
    $extras = $plugins->getPlugins()->toArray();
    $modules = array_merge($extras, (array)$modules);
    $adminRepo = new \Btybug\Console\Repository\AdminPagesRepository();
    $output = "";
    if (count($data)) {
        foreach ($data as $module) {
            if ($moduleCh == null or $moduleCh->namespace == $module->module_id) {
                if (!$module->module_id) {
                    if ($checkbox === true) {
                        $output .= hierarchyAdminPagesListPermissions($adminRepo->main(), true, $icon, $roleSlug);
                    } else {
                        if ($roleSlug == null) {
                            $output .= hierarchyAdminPagesListFull($adminRepo->main(), true, $icon, 0);
                        } else {
                            $output .= hierarchyAdminPagesList($adminRepo->main(), true, $icon, 0, $roleSlug);
                        }
                    }
                } else {
                    $plugins->modules();
                    $value = $plugins->find($module->module_id);
                    if (!$value) {
                        $plugins->plugins();
                        $value = $plugins->find($module->module_id);
                    }
                    if ($value) {
                        if ($checkbox === true) {
                            $output .= hierarchyAdminPagesListPermissions($adminRepo->PagesByModulesParent($value), true, $icon, $roleSlug);
                        } else {
                            if ($roleSlug == null) {
                                $output .= hierarchyAdminPagesListFull($adminRepo->PagesByModulesParent($value), true, $icon, 0);
                            } else {
                                $output .= hierarchyAdminPagesList($adminRepo->PagesByModulesParent($value), true, $icon, 0, $roleSlug);
                            }
                        }
                    }
                }
            }
        }
    }

    return $output;
}

//TODO transver in Console
function hierarchyAdminPagesList($data, $parent = true, $icon = true, $id = 0, $roleSlug = null)
{//dd($roleSlug);
    $children = [];
    $output = ' <ul id="accordion" class="panel-group" data-nav-drag="" role="tablist" aria-multiselectable="true">';

    // Loop through items
    foreach ($data as $item) {//dd($roleSlug);
        if (\Btybug\Console\Services\StructureService::checkAccess($item->id, $roleSlug)) {
            if ($parent) {
                $output .= '<li data-id="' . $item->id . '" data-drag="' . $item->title . '" id="headingOne' . $item->id . '" data-details=\'\' data-name="' . $item->title . '"  data-url=' . $item->url . ' ';

                if (count($item->childs)) {
                    $output .= 'data-child="' . $item->id . '" ';
                }
                $output .= 'class="panel panel-default page_col">';

            } else {
                $output .= '<li data-id="' . $item->id . '" data-details=\'\' data-name="' . $item->title . '" data-url=' . $item->url . ' class="panel panel-default list_items page_col">';
            }

            $output .= '<div class="panel-heading" role="tab" id="headingOne" >';
            $output .= '<h4 class="panel-title">';
            if (count($item->childs)) {
                $output .= '<i class="childitem fa fa-object-group" data-drop="unit"></i>';
            }
            $output .= '<a data-toggle="collapse" data-pagecolid="' . $item->id . '" data-parent="#accordion' . $item->id . '" href="#collapseOne' . $item->id . '" aria-expanded="true" aria-controls="collapseOne" class="link_name collapsed">';
            $output .= $item->title;
            $output .= '</a>';
            $output .= '</h4>';
            $output .= '</div>';
            /* Actions */
            /* Actions END */

            if (count($item->childs)) {
                $output .= '<ul id="collapseOne' . $item->id . '" class="panel-collapse collapse"  role="tabpanel" aria-labelledby="headingOne' . $item->id . '">';
                $output .= '<div class="panel-body">';
                $children = $item->childs;
                $output .= hierarchyAdminPagesList($children, false, $icon, 0, $roleSlug);
                $output .= '</div>';
                $output .= '</ul>';
            }

            $output .= '</li>';
        }
        // If this is the top parent

    }

    $output .= '</ul>';

    // Return data tree
    return $output;
}

//TODO transver in Console
function hierarchyAdminPagesListPermissions($data, $parent = true, $icon = true, $role, $checkbox = false)
{
    $children = [];
    $output = ' <ul class="panel-group" role="tablist" aria-multiselectable="true">';
    // Loop through items
    foreach ($data as $item) {//dd($roleSlug);
        if ($parent) {
            $output .= '<li data-id="' . $item->id . '" class="panel panel-default page_col">';
        } else {
            $output .= '<li data-id="' . $item->id . '" class="panel panel-default list_items page_col">';
        }

        $output .= '<div class="panel-heading" role="tab" id="headingOne" >';
        $output .= '<h4 class="panel-title">';
        $output .= '<a data-toggle="collapse" data-pagecolid="' . $item->id . '" data-parent="#accordion' . $item->id . '" href="#collapseOne' . $item->id . '" aria-expanded="true" aria-controls="collapseOne" class="link_name collapsed">';
        $output .= $item->title;

        if ($role->id === 1) {
            $output .= '<span class="pull-right" style="color:#1fec7e;">All Access</span>';
        } else {
            $permissionRepo = new \Btybug\User\Repository\PermissionRoleRepository();
            if ($item->parent) {
                $parentPerm = \Btybug\Console\Services\StructureService::AdminPagesParentPermissionWithRole($item->id, $role->id);
                if ($parentPerm) {
                    $isChecked = $permissionRepo->getBackendPagesWithRoleAndPage($role->id, $item->id);
                    $output .= "<span class=\"pull-right\">" . Form::checkbox("permission[$role->id][$item->id]", 1, ($isChecked) ? "checked" : null, ['class' => 'show-child-perm', 'data-module' => $item->module_id, 'data-pageid' => $item->id, 'data-roleid' => $role->id, 'data-page-type' => 'back', 'style' => 'left:0;']) . "</span>";
                } else {
                    $output .= '<span class="pull-right" style="color:#ec0e0a;">No Access <i class="fa fa-minus-square"></i></span>';
                }
            } else {
                $isChecked = $permissionRepo->getBackendPagesWithRoleAndPage($role->id, $item->id);
                $output .= "<span class=\"pull-right\">" . Form::checkbox("permission[$role->id][$item->id]", 1, ($isChecked) ? "checked" : null, ['class' => 'show-child-perm', 'data-module' => $item->module_id, 'data-pageid' => $item->id, 'data-roleid' => $role->id, 'data-page-type' => 'back', 'style' => 'left:0;']) . "</span>";
            }
        }

        $output .= '</a>';
        $output .= '</h4>';
        $output .= '</div>';
        /* Actions */
        /* Actions END */
        if (count($item->childs)) {
            $output .= '<ul id="collapseOne' . $item->id . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">';
            $output .= '<div class="panel-body">';
            $children = $item->childs;
            $output .= hierarchyAdminPagesListPermissions($children, false, $icon, $role);
            $output .= '</div>';
            $output .= '</ul>';
        }
        $output .= '</li>';
        // If this is the top parent
    }
    $output .= '</ul>';
    // Return data tree
    return $output;
}

//TODO transver in Manage
function hierarchyAdminPagesListHierarchy($data, $parent = true, $icon = true, $id = 0)
{
    $output = '';
    // Loop through items
    if ($data) {
        foreach ($data as $item) {
            $children = $item->childs;

            $output .= ' <ol class="pagelisting">';
            $output .= '<li data-id="' . $item->id . '">';
            $output .= '<div class="listinginfo">';
            $output .= '<div class="lsitingbutton">';
            $output .= '<a href="' . url('/admin/console/structure/pages/settings', $item->id) . '" class="btn"><i class="fa fa-pencil"></i></a>';
//            if ($item->type == 'custom') {
//                $output .= '<a href="' . url('/admin/manage/frontend/pages/new', $item->id) . '" class="btn"><i class="fa fa-plus"></i></a>';
//                $output .= '<a data-href="' . url('/admin/manage/frontend/pages/delete') . '" data-key="' . $item->id . '" data-type="Page ' . $item->title . '"  class="delete-button btn trashBtn"><i class="fa fa-trash"></i></a>';
//            }
//        $output .= '<a data-toggle="collapse" data-pagecolid="' . $item->id . '" data-parent="#accordion' . $item->id . '" href="#collapseOne' . $item->id . '" aria-expanded="true" aria-controls="collapseOne" class="link_name collapsed">';
//        $output .= $item->title;
//        $output .= '</a>';
            $output .= '</div>';
            $output .= '<button class="btn btn-collapse" type="button" data-caction="collapse">';
            if (count($children)) {
                $output .= '<i class="fa fa-minus" data-collapse="' . $item->id . '" aria-hidden="true"></i>';
            }
            $output .= '</button>';

            $output .= '<span class="listingtitle">' . $item->title . '</span>';
            $output .= '</div>';
            /* Actions */
            /* Actions END */
            if (count($children)) {
                $output .= hierarchyAdminPagesListHierarchy($children, false, $icon, 0);
            }

//        $output .= '</li>';
            // If this is the top parent
            $output .= '</li>';
            $output .= '</ol>';
        }
    }
    // Return data tree
    return $output;
}

//function BBlinkFonts()
//{
//    $helper = new \Btybug\btybug\Helpers\helpers();
//    $fonts = $helper->getFontList();
//    $links = '';
//    if (count($fonts)) {
//        foreach ($fonts as $font) {
//            if (isset($font["items"]['config']))
//                $links .= "<link href='/resources/assets/fonts/" . $font['folder'] . "/" . $font['items']['config']->css . ".css' rel='stylesheet' />";
//        }
//    }
//
//    return $links;
//}
//TODO:find the right direction for this function
function BBgetUnitAttr($id, $key)
{
    $section = \Btybug\btybug\Models\Templates\Units::findByVariation($id);
    if ($section) return $section->{$key};
    return false;

}

function BBgetLayoutAttr($id, $key)
{
    $section = \Btybug\btybug\Models\ContentLayouts\ContentLayouts::findByVariation($id);
    if ($section) return $section->{$key};
    return false;

}

//TODO: move to console module
function BBRegisterAdminPages($module, $title = null, $url, $layout = null, $parent = 0)
{

    $adminPagesRepo = new \Btybug\Console\Repository\AdminPagesRepository();
    if (!$title) $title = $module . ' page title';

    if (substr($url, 0, 6) != "/admin" && substr($url, 0, 5) != "admin") {
        $adminstr = "/admin";
        if (substr($url, 0, 1) != "/") $adminstr = $adminstr . "/";
        $url = $adminstr . $url;
    }

    $page = $adminPagesRepo->create([
        'module_id' => $module,
        'title' => $title,
        'url' => $url,
        'permission' => BBmakeUrlPermission($url),
        'slug' => uniqid(),
        'layout_id' => null,
        'is_default' => 0,
        'parent_id' => $parent
    ]);

    return $page;
}

function BBmakeUrlPermission($url, $sinbol = '.')
{
    if (!$url) return false;
    $url = str_replace('/', $sinbol, $url);
    $url = parametazor($url);
    if (isset($url[0]) && $url[0] == '.') {
        $url = substr($url, 1);
    }

    return $url;
}

function parametazor($url)
{
    preg_match('/{(.*?)}/', $url, $matches);
    if (count($matches)) {
        $url = str_replace($matches[0], 'code_1941', $url);
        preg_match('/{(.*?)}/', $url, $matches2);
        $url = parametazor($url);
    }
    $url = str_replace('code_1941', '{param}', $url);
    return $url;
}

function modules_path($path = '')
{
    return app()->basePath('vendor' . DS . 'sahak.avatar') . ($path ? DS . $path : $path);
}

function BBCheckLoginEnabled()
{
    $settings = \Btybug\btybug\Models\Settings::where('settingkey', 'enable_login')->first();
    if ($settings) {
        return ($settings->val) ? true : false;
    }
    return false;
}

function BBrenderPageContent($settings)
{
    if (!isset($settings['content_type'])) return null;
    if ($settings['content_type'] == 'template') {
        return BBRenderUnits($settings['template']);
    }
    if ($settings['content_type'] == 'editor') {
        return $settings['main_content'];
    }
    return 'Main Content';
}

function BBstyle($path)
{
    $styles = [];
    if (\Session::has('custom.styles')) {
        $styles = \Session::get('custom.styles', []);
    }
    $styles[md5($path)] = $path;
    \Session::put('custom.styles', $styles);
}

function BBscript($path)
{
    $scripts = [];
    if (\Session::has('custom.scripts')) {
        $scripts = \Session::get('custom.scripts', []);
    }
    $scripts[md5($path)] = $path;
    \Session::put('custom.scripts', $scripts);
}

function BBGiveMe($type, $data = null, $index = null)
{
    $type = strtolower($type);
    switch ($type) {
        case 'array':
            return \Btybug\btybug\Models\BBGiveMe::GiveArray($data);
            break;
        case 'string':
            return \Btybug\btybug\Models\BBGiveMe::GiveString($data);
            break;
        case 'int':
            return \Btybug\btybug\Models\BBGiveMe::GiveNumber($data, $index);
            break;
        default:
            print "Enter valid argument!";

    }
}

function BBGetMenu(
    $id
)
{
    $menuRepo = new \Btybug\btybug\Repositories\MenuRepository();

    $menu = $menuRepo->find($id);

    if ($menu) return $menu->items;
}


function hierarchyFrontendPagesListWithModuleName($data, $moduleCh = null, $icon = true, $membershipSlug = null, $checkbox = false)
{
    $plugins = new \Avatar\Avatar\Repositories\Plugins();
    $plugins->modules();
    $modules = $plugins->getPlugins()->toArray();
    $plugins->plugins();
    $extras = $plugins->getPlugins()->toArray();
    $modules = array_merge($extras, (array)$modules);

    $output = "";
    if (count($data)) {
        foreach ($data as $module) {
            if ($moduleCh == null or $moduleCh->slug == $module->module_id) {
                $frontPageRepo = new \Btybug\Console\Repository\FrontPagesRepository();
                if (!$module->module_id) {
                    if ($checkbox === true) {

                        $output .= hierarchyFrontendPagesListPermissions($frontPageRepo->getMain(), true, $icon, $membershipSlug);
                    } else {

                        if ($membershipSlug == null) {
                            $output .= hierarchyAdminPagesListFull($frontPageRepo->getMain(), true, $icon, 0);
                        } else {
                            $output .= hierarchyFrontPagesList($frontPageRepo->getMain(), true, $icon, 0, $membershipSlug);
                        }
                    }
                } else {
                    $plugins->modules();
                    $value = $plugins->find($module->module_id);
                    if (!$value) {
                        $plugins->plugins();
                        $value = $plugins->find($module->module_id);
                    }
                    if ($value) {
                        if ($checkbox === true) {
                            $output .= hierarchyFrontendPagesListPermissions($frontPageRepo->PagesByModulesParent($value), true, $icon, $roleSlug);
                        } else {
                            if ($membershipSlug == null) {
                                $output .= hierarchyAdminPagesListFull($frontPageRepo->PagesByModulesParent($value), true, $icon, 0);
                            } else {
                                $output .= hierarchyFrontPagesList($frontPageRepo->PagesByModulesParent($value), true, $icon, 0, $membershipSlug);
                            }
                        }
                    }
                }
            }
        }
    }

    return $output;
}


function hierarchyFrontPagesList($data, $parent = true, $icon = true, $id = 0, $roleSlug = null)
{
    $children = [];
    $output = ' <ul id="accordion" class="panel-group" data-nav-drag="" role="tablist" aria-multiselectable="true">';
    // Loop through items
    foreach ($data as $item) {//dd($roleSlug);
        if (\Btybug\FrontSite\Services\FrontendPageService::checkAccess($item->id, $roleSlug)) {
            if ($parent) {
                $output .= '<li data-id="' . $item->id . '" data-drag="' . $item->title . '" id="headingOne' . $item->id . '" data-details=\'\' data-name="' . $item->title . '"  data-url=' . $item->url . ' ';

                if (count($item->childs)) {
                    $output .= 'data-child="' . $item->id . '" ';
                }
                $output .= 'class="panel panel-default page_col">';

            } else {
                $output .= '<li data-id="' . $item->id . '" data-details=\'\' data-name="' . $item->title . '" data-url=' . $item->url . ' class="panel panel-default list_items page_col">';
            }

            $output .= '<div class="panel-heading" role="tab" id="headingOne" >';
            $output .= '<h4 class="panel-title">';
            if (count($item->childs)) {
                $output .= '<i class="childitem fa fa-object-group" data-drop="unit"></i>';
            }
            $output .= '<a data-toggle="collapse" data-pagecolid="' . $item->id . '" data-parent="#accordion' . $item->id . '" href="#collapseOne' . $item->id . '" aria-expanded="true" aria-controls="collapseOne" class="link_name collapsed">';
            $output .= $item->title;
            $output .= '</a>';
            $output .= '</h4>';
            $output .= '</div>';
            /* Actions */
            /* Actions END */
            if (count($item->childs)) {
                $output .= '<ul id="collapseOne' . $item->id . '" class="panel-collapse collapse"  role="tabpanel" aria-labelledby="headingOne' . $item->id . '">';
                $output .= '<div class="panel-body">';
                $children = $item->childs;
                $output .= hierarchyFrontPagesList($children, false, $icon, 0, $roleSlug);
                $output .= '</div>';
                $output .= '</ul>';
            }
            $output .= '</li>';
        }
    }
    $output .= '</ul>';
    // Return data tree
    return $output;
}

function hierarchyFrontendPagesListPermissions($data, $parent = true, $icon = true, $membership, $checkbox = false)
{
    $children = [];
    $output = ' <ul class="panel-group" role="tablist" aria-multiselectable="true">';
    // Loop through items
    foreach ($data as $item) {//dd($roleSlug);
        if ($parent) {
            $output .= '<li data-id="' . $item->id . '" class="panel panel-default page_col">';
        } else {
            $output .= '<li data-id="' . $item->id . '" class="panel panel-default list_items page_col">';
        }

        $output .= '<div class="panel-heading" role="tab" id="headingOne" >';
        $output .= '<h4 class="panel-title">';
        $output .= '<a data-toggle="collapse" data-pagecolid="' . $item->id . '" data-parent="#accordion' . $item->id . '" href="#collapseOne' . $item->id . '" aria-expanded="true" aria-controls="collapseOne" class="link_name collapsed">';
        $output .= $item->title;
        $permissionRepo = new \Btybug\User\Repository\PermissionRoleRepository();
        if ($item->parent) {
            $parentPerm = \Btybug\FrontSite\Services\FrontendPageService::FrontPagesParentPermissionWithRole($item->id, $membership->id);
            if ($parentPerm) {
                $isChecked = $permissionRepo->getFrontPagesWithRoleAndPage($membership->id, $item->id);
                $output .= "<span class=\"pull-right\">" . Form::checkbox("permission[$membership->id][$item->id]", 1, ($isChecked) ? "checked" : null, ['class' => 'show-child-perm', 'data-module' => $item->module_id, 'data-pageid' => $item->id, 'data-roleid' => $membership->id, 'data-page-type' => 'front', 'style' => 'left:0;']) . "</span>";
            } else {
                $output .= '<span class="pull-right" style="color:#ec0e0a;">No Access <i class="fa fa-minus-square"></i></span>';
            }
        } else {
            $isChecked = $permissionRepo->getFrontPagesWithRoleAndPage($membership->id, $item->id);
            $output .= "<span class=\"pull-right\">" . Form::checkbox("permission[$membership->id][$item->id]", 1, ($isChecked) ? "checked" : null, ['class' => 'show-child-perm', 'data-module' => $item->module_id, 'data-pageid' => $item->id, 'data-roleid' => $membership->id, 'data-page-type' => 'front', 'style' => 'left:0;']) . "</span>";
        }

        $output .= '</a>';
        $output .= '</h4>';
        $output .= '</div>';
        /* Actions */
        /* Actions END */

        if (count($item->childs)) {
            $output .= '<ul id="collapseOne' . $item->id . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">';
            $output .= '<div class="panel-body">';
            $children = $item->childs;
            $output .= hierarchyFrontendPagesListPermissions($children, false, $icon, $membership);
            $output .= '</div>';
            $output .= '</ul>';
        }

        $output .= '</li>';
        // If this is the top parent

    }

    $output .= '</ul>';

    // Return data tree
    return $output;
}

function BBRenderArea($settings, $key)
{
    if (isset($settings[$key]['content_type'])) {
        if ($settings[$key]['content_type'] == 'template') {
            return BBRenderUnits($settings[$key][$key]);
        } else {
            echo $settings[$key]['editor'];
        }
    }
}

$_PLUGIN_PROVIDERS = [];
function addProvider($provider, $options = [], $force = false)
{
    global $_PLUGIN_PROVIDERS;
    $providers = isset($_PLUGIN_PROVIDERS['pluginProviders']) ? $_PLUGIN_PROVIDERS['pluginProviders'] : [];
    $providers[$provider] = compact('options', 'force');
    $_PLUGIN_PROVIDERS['pluginProviders'] = $providers;
}

function BBrenderHook($id)
{
    $hookRepository = new \Btybug\btybug\Repositories\HookRepository();
    $html = $hookRepository->render($id);
    return $html;
}

function has_setting($settings, $setting, $compare = false)
{

    if (!isset($settings[$setting])) return false;
    if ($compare) {
        if ($settings[$setting] != $compare) return false;
    }

    return true;
}

function get_settings($settings, $setting, $default = '')
{

    if (has_setting($settings, $setting)) {
        return $settings[$setting];
    }

    return $default;
}

function form_render($attr)
{
    $formRepo = new \Btybug\Console\Repository\FormsRepository();
    $form = $formRepo->findByIdOrSlug($attr);

    if ($form) {
        return \Btybug\Console\Services\FormService::renderFormBlade($form->slug);
    }
}

function generate_special_page(array $data){
    return \Btybug\FrontSite\Services\FrontendPageService::generateSpecialPage(
        $data
    );
}