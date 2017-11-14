<?php
/**
 * Copyright (c) 2016.
 * *
 *  * Created by PhpStorm.
 *  * User: Edo
 *  * Date: 10/3/2016
 *  * Time: 10:44 PM
 *
 */

namespace Btybug\btybug\Http\Controllers\Admincp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Btybug\btybug\Helpers\helpers;
use Btybug\btybug\Models\ContentLayouts\ContentLayouts;
use Btybug\btybug\Models\Templates\Sections;
use Btybug\btybug\Models\Templates\Units;
use Btybug\btybug\Models\Widgets;
use Btybug\btybug\Repositories\MenuRepository;
use Btybug\btybug\Services\CmsItemReader;
use View;


/**
 * Class DashboardController
 *
 * @package App\Http\Controllers\Admincp
 */
class ModalityController extends Controller
{

    /**
     * @var helpers
     */
    private $helpers;
    private $menuRepo;


    /**
     * ModalityController constructor.
     * @param Widget $widget
     */
    public function __construct(MenuRepository $menuRepository)
    {
        $this->helpers = new helpers;
        $this->menuRepo = $menuRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function postSettingsLive(Request $request)
    {
        $actions = [
            'styles' => 'getStyles',
            'widgets' => 'getWidgets',
            'menus' => 'getMenus',
            'icons' => 'getIcons',
            'templates' => 'getTpls',
            'theme' => 'getTheme',
            'unit' => 'getUnit',//working with tags
            'units' => 'getUnits',
            'files' => 'getFiles',
            'page_sections' => 'getPageSections',
            'sections' => 'getSections',
            'main_body' => 'getMainBody',
            'layouts' => 'getLayouts'//working with tags
        ];

        $data = $request->all();
        if (isset($actions[$data['action']])) {
            $function = $actions[$data['action']];
            return $this->$function($data);
        }

        return ['error' => 'action not found'];
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTpls($data)
    {
        $key = $data['key'];

        isset($data['place']) ? $place = $data['place'] : $place = 'frontend';
        isset($data['type']) ? $type = $data['type'] : $type = 'header';

        if ($place == 'frontend') {
            $templates = CmsItemReader::getAllGearsByType('hf')
                ->where('place', $place)
                ->where('type', $type)
                ->run();
        } else {
            $templates = CmsItemReader::getAllGearsByType('templates')
                ->where('place', $place)
                ->where('type', $type)
                ->run();
        }


        if (!count($templates)) return \Response::json(['error' => true]);

        $html = View::make('btybug::styles.templates', compact('templates'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMenus($data)
    {
        $key = $data['key'];
        isset($data['type']) ? $type = $data['type'] : $type = 'frontend';

        $menus = $this->menuRepo->getBy('place', $type);

        if (!count($menus)) return \Response::json(['error' => true]);

        $html = View::make('btybug::styles.menus', compact('menus'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnit($data)
    {
        $key = $data['type'];
        $units = Units::all()->sortByTag($key);

        if (!count($units)) return \Response::json(['error' => true]);
        if(isset($data['multiple']) && $data['multiple'] == true){
            $html = View::make('btybug::styles.multiple-units', compact('units', 'data'))->render();
        }else{
            $html = View::make('btybug::styles.units', compact('units', 'data'))->render();
        }

        return \Response::json(['error' => false, 'html' => $html]);
    }

    public function getUnits($data)
    {
        $key = $data['key'];
        isset($data['type']) ? $type = $data['type'] : $type = 'frontend';

        if (isset($data['sub'])) {
            $units = CmsItemReader::getAllGearsByType('units')
                ->where('place', $type)
                ->where('type', $data['sub']);
        } else {
            $units = CmsItemReader::getAllGearsByType('units')
                ->where('place', $type);
        }

        if (isset($data['item'])) {
            $units->where('slug', $data['item']);
        }

        if (isset($data['module'])) {
            $units->where('module_slug', $data['module']);
        }

        if (isset($data['mt'])) {
            $units->where('main_type', $data['mt']);
        }

        if (isset($data['group'])) {
            $units->where('group', $data['group']);
        }
        $units = $units->run();
        if (isset($data['except'])) {
            $except = json_decode($data['except'], true);
            if ($units && count($units) && $except && !empty($except)) {
                $unitsArr = [];
                foreach ($except as $key => $setting) {
                    $unitsArr[] = explode('.', $setting)[0];
                }
                foreach ($units as $key => $blogUnit) {
                    if (in_array($blogUnit->slug, $unitsArr)) {
                        unset($units[$key]);
                    }
                }
            }
        }
        if (!count($units)) return \Response::json(['error' => true]);
        $html = View::make('btybug::styles.units', compact('units', 'data'))->render();
        return \Response::json(['error' => false, 'html' => $html]);
    }

    public function getFiles($data)
    {
        $key = $data['key'];
        isset($data['type']) ? $type = $data['type'] : $type = 'fields';

        $files = FilesBB::getAllFiles();

        if (!count($files)) return \Response::json(['error' => true]);

        $html = View::make('btybug::styles.units_files', compact('files'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWidgets($data)
    {
        $key = $data['key'];

        if (isset($data['item'])) {
            $templates = Widgets::where('data_source', $data['item'])->run();
        } else {
            isset($data['type']) ? $type = $data['type'] : $type = 'others';

            if (isset($data['sub'])) {
                $templates = Widgets::where('general_type', $data['sub'])->where('type', $type)->run();
            } else {
                $templates = Widgets::where('general_type', $type)->run();

            }
        }


        if (!count($templates)) return \Response::json(['error' => true]);

        $html = View::make('btybug::styles.widgets', compact('templates'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTheme($data)
    {
        $layouts = Themes::active()->layouts();
        $html = View::make('btybug::styles.theme', compact('layouts'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIcons($data)
    {
        $fonts = $this->helpers->getFontList();
        if (!count($fonts)) return \Response::json(['error' => true]);
        //dd($fonts);
        $html = View::make('btybug::styles.icons', compact('fonts'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStyles($data)
    {
        $key = $data['key'];
        isset($data['type']) ? $type = $data['type'] : $type = 'text';
        $types = config('admin.admin_styles');
        if (!isset($types[$type])) return \Response::json(['error' => true]);

        $styles = $types[$type];
        if (!count($styles)) return \Response::json(['error' => true]);
        $items = Style::where('type', $type)->where('sub', key($styles))->get();

        $html = View::make('btybug::styles.styles', compact('styles', 'items', 'type'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    public function getPageSections($data)
    {
        $key = $data['key'];
        isset($data['type']) ? $type = $data['type'] : $type = 'frontend';
        $layouts = CmsItemReader::getAllGearsByType('page_sections')
            ->where('place', $type)
            ->run();
        if (!count($layouts)) return \Response::json(['error' => true]);
        $html = View::make('btybug::styles.page_sections', compact('layouts'))->render();
        return \Response::json(['error' => false, 'html' => $html]);
    }

    public function getSections($data)
    {

        $key = $data['key'];
        isset($data['type']) ? $type = $data['type'] : $type = 'horizontal';
        isset($data['place']) ? $place = $data['place'] : $place = 'frontend';
        $sections = CmsItemReader::getAllGearsByType('sections')
            ->where('place', $place)
            ->where('type', $type)
            ->run();
        if (!count($sections)) return \Response::json(['error' => true]);
        $html = View::make('btybug::styles.sections', compact('sections'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    public function getMainBody($data)
    {

        $key = $data['key'];

        isset($data['sub']) ? $type = $data['sub'] : $type = 'general';
        isset($data['place']) ? $place = $data['place'] : $place = 'frontend';

        $main_body = CmsItemReader::getAllGearsByType('main_body')
            ->where('place', $place)
            ->where('type', $type)
            ->run();

        if (!count($main_body)) return \Response::json(['error' => true]);

        $html = View::make('btybug::styles.main_body', compact('main_body'))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postPageSectionOptions(Request $request)
    {
        $id = $request->get('id');
        $layout = ContentLayouts::find($id);
        if (!$layout) return \Response::json(['error' => true]);
        $items = $layout->variations();
        $ajax = true;
        $html = View::make('btybug::styles.page_sections', compact(['items', 'ajax']))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postSectionOptions(Request $request)
    {
        $id = $request->get('id');
        $section = Sections::find($id);
        if (!$section) return \Response::json(['error' => true]);
        $items = $section->variations();
        $ajax = true;
        $html = View::make('btybug::styles.sections', compact(['items', 'ajax']))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postMainBodyOptions(Request $request)
    {
        $id = $request->get('id');
        $main_body = $currentMainBody = CmsItemReader::getAllGearsByType('main_body')
            ->where('place', 'frontend')
            ->where('slug', $id)
            ->first();
        if (!$main_body) return \Response::json(['error' => true]);
        $items = $main_body->variations();
        $ajax = true;
        $html = View::make('btybug::styles.main_body', compact(['items', 'ajax']))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function psotStylesOptions(Request $request)
    {
        $sub = $request->get('id');
        $type = $request->get('key');

        $items = Style::where('type', $type)->where('sub', $sub)->get();
        $ajax = true;
        $html = View::make('btybug::styles.styles', compact(['items', 'ajax']))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postTplOptions(Request $request)
    {
        $id = $request->get('id');

        $tpl = CmsItemReader::getAllGearsByType()
            ->where('slug', $id)
            ->first();

        if (!$tpl) return \Response::json(['error' => true]);
        $items = $tpl->variations();
        $ajax = true;
        $html = View::make('btybug::styles.templates', compact(['items', 'ajax']))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUnitOptions(Request $request)
    {
        $id = $request->get('id');
        $unit = Units::find($id);
        $key = $request->key;
        if (!$unit) return \Response::json(['error' => true]);
        $items = $unit->variations();
        $ajax = true;
        $html = View::make('btybug::styles.units', compact(['items', 'ajax', 'key']))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function psotWidgetsOptions(Request $request)
    {
        $id = $request->get('id');

        $tpl = Widgets::find($id);

        if (!$tpl) return \Response::json(['error' => true]);
        $items = $tpl->variations();
        $ajax = true;
        $html = View::make('btybug::styles.widgets', compact(['items', 'ajax', 'tpl']))->render();

        return \Response::json(['error' => false, 'html' => $html]);
    }

    /**
     * @param Request $request
     * @return string
     */
    public function postMenusOptions(Request $request)
    {
        $id = $request->get('id');

        $menus = BackendMenus::all();

        $html = View::make('settings::_partials.menus', compact(['menus']))->render();

        return $html;
    }

    public function getLayouts($data)
    {
        $tag = $data['type'];
        $layouts = ContentLayouts::sortByTag($tag);
        if (!count($layouts)) return \Response::json(['error' => true]);
        $html = View::make('btybug::styles.page_sections', compact('layouts'))->render();
        return \Response::json(['error' => false, 'html' => $html]);
    }

}
