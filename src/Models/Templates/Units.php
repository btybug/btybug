<?php namespace Sahakavatar\Cms\Models\Templates;

use App\Core\CmsItemReader;
use App\Models\ContentLayouts\autoinclude;
use App\Models\Templates\Eloquent\Abstractions\TplModel;
use App\Models\Templates\UnitsVariations;
use App\Models\Templates\Templates;
use App\Modules\Resources\Models\Files\traits\FilesPreview;

class Units extends TplModel
{
    use autoinclude;

    protected $tplpath = 'resources/units';

    protected $config = 'config.json';

    public $variationPath = 'variations';

    public static $type = 'units';


    public function variations()
    {
        return $this->allVars('App\Models\Templates\UnitsVariations');
    }

    public function makeVariation($array)
    {
        $vars = new TplVariations();
        return $vars->createVariation($this, $array);
    }
    public function scopeMakeVariation(array $array=[])
    {
        $vars = new TplVariations();
        return $vars->createVariation($this, $array);
    }

    public static function findUnit($slug) {
        $slug = explode('.', $slug);
        if(!isset($slug[0])) {
            return null;
        }
        return self::find($slug[0]);
    }

    public static function getAllUnits()
    {
        $templates = [];
        $ui_units = self::all()->run();
        $tpl = new Templates();
        $tpl_units = $tpl->recursiveFindAllUnits();
        foreach ($ui_units as $unit) {
            $templates[] = $unit;
        }
        foreach ($tpl_units as $unit) {
            $templates[] = $unit;
        }
        $data = new static;
        $data->before = collect($templates);
        return $data;
    }

    public static function getAllUnitsPluck()
    {
        $templates = [];
        $ui_units = self::all()->run();
        $tpl = new Templates();
        $tpl_units = $tpl->recursiveFindAllUnits();
        foreach ($ui_units as $unit) {
            $templates[$unit->slug] = $unit->title;
        }
        foreach ($tpl_units as $unit) {
            $templates[$unit->slug] = $unit->title;
        }
        $data = new static;
        $data->before = collect($templates);
        return $data;
    }

    public static function deleteVariation($id)
    {
        $slug = explode('.', $id);
        $tpl = self::find($slug[0]);
        return UnitsVariations::delete($id, $tpl);
    }

    public static function findVariation($id)
    {
        $slug = explode('.', $id);
        $variation = new UnitsVariations();

        $tpl = self::find($slug[0]);
        return $variation->findVarition($tpl, $id);
    }

    public static function findByVariation($id)
    {
        $slug = explode('.', $id);
        $variation = new UnitsVariations();

        $tpl = self::find($slug[0]);
        return $tpl;
    }

    public function renderSettings(array $variables = [])
    {
        $path = $this->path;
        $variables['tplPath'] = $path;
        $variables['_this']=$this;
        $slug = $this->slug;
        if (!\File::exists($path . '/settings.blade.php')) return "Undefined Settings Blade!";
        \View::addLocation(realpath($this->path));
        \View::addNamespace("$slug", realpath($this->path));
        return \View::make("$slug::settings")->with($variables)->render();
    }

    /**
     * @param $slug
     * @return mixed
     */
    public static function renderLivePreview($slug = NULL, $type = 'frontend')
    {
        $ui = Units::findUnit($slug);
        if(!$ui) {
            return false;
        }
        $variation = self::findVariation($slug);
//        $ui = CmsItemReader::getAllGearsByType('units')->where('slug', $slug)
//        if (!$variation && $ui) {
//            $variation = new UnitsVariations();
//            $data['variation'] = $variation->createVariation($ui, []);
//            $slug = $data['variation']->id;
//        }
        $settings = (isset($variation->settings) && $variation->settings) ? $variation->settings : [];
        $body = url('/admin/console/backend/units/settings-iframe', $slug);
        $dataSettings = url('/admin/console/backend/units/settings-iframe', $slug) . '/settings';
        if($type = 'frontend') {
            $body = url('/admin/uploads/gears/units/settings-iframe', $slug);
            $dataSettings = url('/admin/uploads/gears/units/settings-iframe', $slug) . '/settings';
        }
        $data['body'] = $body;
        $data['settings'] = $dataSettings;
        return view('console::backend.gears.units.preview', compact(['ui', 'id', 'data', 'settings', 'variation']));
    }

    /**
     * @param $slug
     * @param null $title
     * @param $data
     * @param null $isSave
     * @return array|bool
     */
    public static function saveSettings($slug, $title = NULL, $data, $isSave = NULL) {
        if($isSave && $isSave == 'save') {
            $unit = self::findUnit($slug);
            $existingVariation = self::findVariation($slug);
            $dataToInsert = [
                'title' => $title,
                'settings' => $data
            ];
            if(!$existingVariation) {
                $variation = new UnitsVariations();
                $variation = $variation->createVariation($unit, $dataToInsert);
            } else {
                $existingVariation->title = $title;
                $existingVariation->settings = $dataToInsert['settings'];
                $variation = $existingVariation;
            }
            if(!$variation->settings) {
                $variation->setAttributes('settings', []);
            }
            $settings = (isset($variation->settings) && $variation->settings) ? $variation->settings : [];
            if($variation->save()) {
                //dd($variation->id);
                return [
                    'html' => $unit->render(['settings' => $settings, 'source' => BBGiveMe('array', 5), 'cheked' => 1]),
                    'slug' => $variation->id
                ];
            }
        } else {
            return [
                'html' => self::findByVariation($slug)->render(['settings' => $data]),
                'slug' => $slug
            ];
        }
        return false;
    }



}