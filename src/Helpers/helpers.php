<?php

namespace Btybug\btybug\Helpers;

use Auth;
use DB;
use File;
use Btybug\btybug\Models\Urlmanager;
use Btybug\User\Models\Roles;
use Session;

class helpers
{

    private static $ordering = ['asc', 'desc'];
    private $results = [];
    private $setting;
    private $tpl_path = null;
    private $tpl_cache = null;

    public function __construct()
    {
        $this->tpl_path = config('paths.templates_path') . "/";
        $this->tpl_cache = config('paths.templates_cache');
    }

    public static function sorting($data)
    {
        if (!$data) return ['key' => 'created_at', 'value' => 'asc'];

        $data = explode('.', $data);
        if (!isset($data[1]) && !in_array($data[1], self::$ordering)) return ['key' => $data[0], 'value' => 'asc'];

        return ['key' => $data[0], 'value' => $data[1]];
    }


    static function randWithTime()
    {
        return substr("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ", mt_rand(0, 50), 1) . substr(
                md5(time()),
                1
            );
    }

    public static function getDefId($table, $id = 'id')
    {
        $obj = \DB::table($table)->select($id)->first();
        if ($obj === null) {
            return null;
        } else {
            return $obj->$id;
        }
    }

    public static function getRowByCol($table, $col, $val)
    {
        $obj = \DB::table($table)->where($col, $val)->first();
        if ($obj === null) {
            return null;
        } else {
            return $obj;
        }
    }

    public static function getRow($table, $id)
    {
        $obj = \DB::table($table)->find($id);
        if ($obj === null) {
            return null;
        } else {
            return $obj;
        }
    }

    public static function getLayout()
    {
        return ['left' => 'Left Sidebar', 'right' => 'Right Sidebar', 'both' => 'Two Sidebar', 'blank' => 'No Sidebar'];
    }

    public static function sfa($key, $arr)
    {
        if (array_key_exists($key, $arr)) {
            return $arr[$key];
        } else {
            return "";
        }
    }

    public static function formatDate($date, $format = null)
    {
        if ($format) {
            date($format, strtotime($date));
        }

        $settings = DB::table('settings')->where('section', 'setting_system')->where(
            'settingkey',
            'date_format'
        )->first();
        if ($settings) {
            if (strpos($settings->val, '%') !== false) {
                return strftime($settings->val, strtotime($date));
            } else {
                return date($settings->val, strtotime($date));
            }
        }

        return date('m/d/Y', strtotime($date));
    }

    public static function formatTime($time)
    {
        $settings = \Db::table('settings')->where('section', 'setting_system')->where(
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

    public static function studyString($val)
    {
        $val = str_replace('_', ' ', $val);

        return ucwords($val);
    }

    public static function underScore($val)
    {
        return str_replace(' ', '_', $val);
    }

    public static function between($thisIs, $that, $inthat)
    {
        return self::before($that, self::after($thisIs, $inthat));
    }

    public static function before($thisIs, $inthat)
    {
        return substr($inthat, 0, strpos($inthat, $thisIs));
    }

    public static function after($thisIs, $inthat)
    {
        if (!is_bool(strpos($inthat, $thisIs)))
            return substr($inthat, strpos($inthat, $thisIs) + strlen($thisIs));
    }

    public static function between_last($thisIs, $that, $inthat)
    {
        return self::after_last($thisIs, self::before_last($that, $inthat));
    }

    public static function after_last($thisIs, $inthat)
    {
        if (!is_bool(self::strrevpos($inthat, $thisIs)))
            return substr($inthat, self::strrevpos($inthat, $thisIs) + strlen($thisIs));
    }

    public static function strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) return false;
        else return strlen($instr) - $rev_pos - strlen($needle);
    }

    public static function before_last($thisIs, $inthat)
    {
        return substr($inthat, 0, self::strrevpos($inthat, $thisIs));
    }

    public static function getFontList()
    {
        $assets = [];

        $fonts = File::directories('resources/assets/fonts');
        foreach ($fonts as $font) {
            if (file_exists($font . '/config.json')) {
                $config = json_decode(file_get_contents($font . '/config.json'));
                $items = self::getFontIcons(basename($font));
                $assets[] = [
                    'title' => $config->title,
                    'folder' => basename($font),
                    'items' => $items
                ];
            }
        }

        return $assets;
    }

    public static function getFontIcons($folder)
    {
        $list = $config = [];

        $folder = 'resources/assets/fonts/' . $folder;
        if (file_exists($folder . '/config.json')) {
            $config = json_decode(file_get_contents($folder . '/config.json'));
            $list = json_decode(file_get_contents($folder . '/list.json'), true);
        }

        return ['config' => $config, 'list' => $list];
    }

    public static function Info($module)
    {
        $path = config('paths.modules_path');

        if (\File::isDirectory($path . $module->basename)) {
            $size = \File::size($path . $module->basename);
            $size = 1024 / $size;
            $count = count(\File::allFiles($path . $module->basename));
            $controllers = count(\File::allFiles($path . $module->basename . '/Http/Controllers'));
            $views = count(\File::allFiles($path . $module->basename . '/Resources/Views'));

            return [
                'size' => $size,
                'count' => $count,
                'controllers' => $controllers,
                'views' => $views,
            ];
        }

        return false;
    }




    // Get Array and Key Name
    // Return Key Value
    // sfa = set from array 

    public static function readDir($dir)
    {
        $array = [];
        foreach (glob($dir . '/*.*') as $file) {
            dd($file);
            $array[] = $file;
        }

        return $array;
    }

    public static function rglob($dir)
    {
        $files = File::allFiles($dir);

        return $files;
    }

    public function Formatedsettings()
    {
        $settings = $this->getGeneralSettings();
        $this->results = [];
        if ($settings->header_color != '') {
            $this->results['bg'] = 'style="background:' . $settings->header_color . '"';
        }

        if ($settings->header_style == 'sticky') {
            $this->results['sticky_class'] = 'navbar-fixed-top';
            $this->results['sticky_margin'] = '<div class="height-100">&nbsp;</div>';
        } else {
            $this->results['sticky_class'] = '';
            $this->results['sticky_margin'] = '';
        }

        if ($settings->header_layout == 'width') {
            $this->results['container_class'] = 'container';
        } else {
            $this->results['container_class'] = 'container-fluid';
        }

        return $this->results;
    }

    public function getGeneralSettings()
    {
        $id = $this->activeThme();
        $raw_settings = $this->setting->where('theme_id', $id)->first();

        $header = $this->getTplFile(
            $raw_settings->template_id,
            'header'
        ); //$this->formatTemplate($raw_settings->template_id);
        $raw_settings->header_tpl = $header;
        $footer = $this->getTplFile($raw_settings->footer_tpl, 'footer');
        $raw_settings->footer_tpl = $footer;

        return $raw_settings;
    }

    public function activeThme()
    {
        $id = 1;
        $theme = DB::table('themes')->where('status', 'true')->first();
        if ($theme) {
            $id = $theme->id;
        }

        return $id;
    }

    public function getTplFile($tpl_id, $file = '')
    {
        if (!File::exists($this->tpl_cache)) {
            File::makeDirectory($this->tpl_cache, 0777, true);
        }
        $file = $this->tplCreate($tpl_id, $file);

        return $file;
    }

    public function tplCreate($template_id, $file = '', $sec = '')
    {
        $obj = \DB::table('templates')->find($template_id);
        if ($obj) {
            $setting_contents = unserialize($obj->setting_contents);
            if (File::exists($this->tpl_path . $obj->folder_name . "/tpl.blade.php")) {
                $file_contents = File::get($this->tpl_path . $obj->folder_name . "/tpl.blade.php");
                $file_contents = $this->coreFormat($file_contents, $obj->folder_name);
                if (is_array($setting_contents)) {
                    foreach (@$setting_contents as $key => $val) {
                        $file_contents = str_replace('[[' . $key . ']]', $val, $file_contents);
                    }
                }
                File::put($this->tpl_cache . $sec . $template_id . ".blade.php", $file_contents);
            }
            $file = 'tplcache.' . $sec . $template_id;
        }

        return $file;
    }

    public function coreFormat($file_contents, $folder_name)
    {
        $pre_defineds = ['[[tpl_images]]' => 'images', '[[tpl_js]]' => 'js', '[[tpl_css]]' => 'css'];
        foreach ($pre_defineds as $key => $val) {
            $file_contents = str_replace(
                $key,
                url() . "/appdata/app/packages/Hussain/Templates/src/" . $folder_name . "/" . $val,
                $file_contents
            );
        }

        return $file_contents;
    }

    public function getAssestFile($id)
    {
        $id = str_replace(",", "", $id);
        $assest = DB::table('assests')->find($id);
        if ($assest) {
            return $assest->folder;
        } else {
            return '';
        }
    }

    public function isvalidpage($page_id)
    {
        $active_theme = $this->activeThme();
        $page = Themepage::where('theme_id', $active_theme)->where('page_id', $page_id)->first();
        if ($page) {
            return 1;
        } else {
            return 0;
        }
    }

    public function getCorePageSettings($code = "", $active_theme = 1)
    {
        $this->results = [];
        $active_theme = $this->activeThme();
        $corepage = Corepage::where('code', $code)->first();
        $page_settings = Themepage::where('theme_id', $active_theme)->where('page_id', $corepage->id)->first();

        $general_settings = $this->getGeneralSettings();
        if ($page_settings) {
            $layout = $page_settings->page_layout;

            $this->results['contant_width'] = 12;
            if ($layout == 'left') {
                $this->results['has_left'] = 1;

                // THIS IS GIVING ERROR - FIX it, I've set empty string for run CMS

                $this->results['left_contents'] = '';//barHtm($page_settings->left_bar);
                $this->results['contant_width'] = 12 - $general_settings->lnav_grid;
            }
            if ($layout == 'right') {
                $this->results['has_right'] = 1;
                $this->results['right_contents'] = '';//barHtm($page_settings->right_bar);
                $this->results['contant_width'] = 12 - $general_settings->rnav_grid;
            }

            if ($layout == 'both') {
                $this->results['has_left'] = 1;
                $this->results['has_right'] = 1;
                $this->results['contant_width'] = 12 - ($general_settings->rnav_grid + $general_settings->lnav_grid);
            }


            if ($page_settings->content_type == 'template' && $page_settings->page_tpl > 0) {
                $this->results['content_type'] = 'template';
                $this->results['tpl_contents'] = $this->getTplFile($page_settings->page_tpl);
            } else {
                $this->results['content_type'] = 'wysiwyg';
                $this->results['tpl_contents'] = $page_settings->page_contents;
            }
            $this->results['valid'] = 1;
        } else {
            $this->results['valid'] = 0;
        }

        $this->results['general_settings'] = $general_settings;

        return $this->results;
    }

    /**
     * @param $template_id
     * @return string
     */
    public function formatTemplate($template_id)
    {
        $obj = \DB::table('templates')->find($template_id);

        if ($obj) {
            $setting_contents = unserialize($obj->setting_contents);
            $file_contents = file_get_contents($this->tpl_path . $obj->folder_name . "/tpl.blade.php");
            $file_contents = $this->coreFormat($file_contents, $obj->folder_name);
            if (is_array($setting_contents)) {
                foreach (@$setting_contents as $key => $val) {
                    $file_contents = str_replace('[[' . $key . ']]', $val, $file_contents);
                }
            }

            return $file_contents;
        } else {
            return '';
        }
    }

    public function getCustomPageSettings($page)
    {
        $this->results = [];
        $general_settings = $this->getGeneralSettings();
        if ($page) {
            $layout = $page->layout;
            $this->results['contant_width'] = 12;

            if ($layout == 'left') {
                $this->results['has_left'] = 1;
                $this->results['contant_width'] = 12 - $general_settings->lnav_grid;
                $this->results['left_side_bar'] = $this->formatSidebar($page->l_side_bar_id);
            }
            if ($layout == 'right') {
                $this->results['has_right'] = 1;
                $this->results['contant_width'] = 12 - $general_settings->rnav_grid;
            }

            if ($layout == 'both') {
                $this->results['has_left'] = 1;
                $this->results['has_right'] = 1;
                $this->results['contant_width'] = 12 - ($general_settings->rnav_grid + $general_settings->lnav_grid);
            }
        }

        $this->results['general_settings'] = $general_settings;

        return $this->results;
    }


    // Get template data by ID

    public function formatSidebar($id)
    {
        $data = "";
        if ($id > 0) {
            $obj = \DB::table('sidebars')->find($id);

            return "ABC";
        }

        return $data;
    }

    function formatBytes($size, $precision = 0)
    {
        $base = log($size, 1024);
        $suffixes = array('', 'k', 'M', 'G', 'T');

        return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
    }

    public function statusdd($selected = '')
    {
        $data = ['' => 'All Status', 'active' => 'Active', 'suspended' => 'Suspended'];

        return $this->ddbyarray($data, $selected);
    }

    function ddbyarray($data, $selected = '')
    {
        $srt = '';
        foreach ($data as $key => $val) {
            if ($key != $selected) {
                $srt .= '<option value="' . $key . '">' . $val . '</option>';
            } else {
                $srt .= '<option value="' . $key . '" selected="selected">' . $val . '</option>';
            }
        }

        return $srt;
    }

    public function updatesession($msg = "Updated Successfully!", $class = "alert-success")
    {
        Session::flash('message', $msg);
        Session::flash('alert-class', $class);
    }

    public function basepath()
    {
        $bse_path = base_path();
        $bse_path = substr($bse_path, 0, -7);

        return $bse_path;
    }

    public function safeDeleteFolder($paths)
    {
        foreach ($paths as $path) {
            if (File::isDirectory($path)) {
                File::deleteDirectory($path);
            }
        }
    }

    public function clearWidgetsInPages($wdiget)
    {
        $pages = Pagesetting::all();
        if ($pages) {
            foreach ($pages as $page) {
                if (str_contains($page->fisrt_col_widget, $wdiget)) {
                    $page->fisrt_col_widget = str_replace($wdiget, '', $page->fisrt_col_widget);
                    $page->save();
                }
                if (str_contains($page->sec_col_widget, $wdiget)) {
                    $page->sec_col_widget = str_replace($wdiget, '', $page->sec_col_widget);
                    $page->save();
                }
            }
        }
    }

    public function tplDelete($template_id)
    {
        $this->clearTplCache();
    }

    public function clearTplCache()
    {
        File::cleanDirectory($this->tpl_cache);
    }

    public function formatMenu($menu, $user_role = "")
    {

        $data_arr = unserialize($menu['html_data']);

        if ($user_role != "") {
            $menu_data = $data_arr[$user_role];
        } else {
            if (Auth::check()) {
                $user_type = $this->getUserType();
                $menu_data = $data_arr['after'][$user_type];
            } else {
                $menu_data = $data_arr['before'];
            }
        }

        $view = view('menus.' . $menu['type'], compact(['menu_data']));

        return $view->render();
    }

    public function getUserType()
    {
        if (Auth::check()) {
            if (!Session::has('user_role')) {
                session(['user_role' => Auth::user()->roles->first()->slug]);
            }

            return session('user_role');
        } else {
            return '';
        }
    }

    public function getContentType()
    {
        return ['wysiwyg' => 'WYSIWYG', 'template' => 'Template'];
    }

    /**
     *
     */
    public function getEmailReceivers()
    {
        $receivers = Roles::pluck('slug', 'slug')->toArray();

        $receivers[] = 'Requested Email';
        $receivers[] = 'Logged  User';
        $receivers[] = 'Signup User';

        return implode(",", $receivers);
    }

    public function getTemplateData($template_id)
    {
        $obj = \DB::table('templates')->find($template_id);

        return $obj;
    }

    public function mkChkBox()
    {
        return '<input name="plugin" class="del_select" type="checkbox" value="{!! $id !!}" />';
    }

// use strrevpos function in case your php version does not include it

    /**
     * @param $page_id
     * @param $url
     * @param $type
     * @return static
     */
    public function updateUrl($page_id, $url, $type, $parent_id = 0)
    {
        $um = Urlmanager::where('page_id', $page_id)->first();
        if ($um) {
            $um->url = $url;
            $um->save();
        } else {
            $page = Corepage::find($page_id);
            if ($page) {
                $this->addUrl($page_id, $url, $page->page_type . "_page");
            }
        }

    }

    /**
     * @param $page_id
     * @param $url
     * @param $type
     * @return static
     */
    public function addUrl($page_id, $url, $type, $parent_id = 0)
    {
        if ($changed = $this->checkUrl($url)) {
            $url = $changed;
        }

        $record = Urlmanager::create(
            [
                'page_id' => $page_id,
                'url' => $url,
                'type' => $type,
                'parent_id' => $parent_id
            ]
        );

        return ($record) ? $record : null;
    }

    public function checkUrl($url)
    {
        $check = Urlmanager::where('url', $url)->first();
        if ($check) {
            $url = $url . substr(md5(rand()), 0, 5);

            return $url;
        }

        return false;
    }

    /**
     * @param $page_id
     * @return bool
     */
    public function removeUrl($page_id, $url = null)
    {
        if ($url) {
            $u = Urlmanager::where('url', $url)->first();
            if ($u) {
                $u->delete();
            }
        } else {
            $url = Urlmanager::where('page_id', $page_id)->first();
            if ($url) {
                if (count($url->childs())) {
                    foreach ($url->childs() as $ch) {
                        $ch->delete();
                    }
                }
                $url->delete();

                return true;
            }
        }


        return false;
    }

    public function common_settings()
    {
        $assets = [
            'header' => [
                'bootstrap'
            ],
            'footer' => []
        ];
        // Assets Rendering
        if (isset($assets['header'])) {
            \Assets::registerCollection('header', $assets['header']);
        }

        if (isset($assets['footer'])) {
            \Assets::registerCollection('footer', $assets['footer']);
        }
        $layout = (object)[];
        $layout->settings_css = '';
        $layout->settings_data = '';
        $layout->has_header = 1;
        $layout->has_footer = 1;
        $layout->has_sidebar1 = 0;
        $layout->has_sidebar2 = 0;
        $header = '';
        $footer = '';
        $preview_class = '';
        $customiser_css = '';
        return compact(['layout', 'header', 'footer', 'preview_class', 'customiser_css']);
    }

    public function mkEditAble($class, $col, $datatype = 'text')
    {
        return '<a data-name="' . $col . '" data-pk="' . $class->id . '" style="cursor: pointer;" class="editable" data-type="' . $datatype . '">' . $class->$col . '</a>';
    }
}