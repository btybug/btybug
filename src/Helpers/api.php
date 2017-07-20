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