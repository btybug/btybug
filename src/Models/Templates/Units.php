<?php namespace Btybug\btybug\Models\Templates;

use Avatar\Avatar\Repositories\Plugins;
use Btybug\btybug\Models\ContentLayouts\autoinclude;
use Btybug\btybug\Models\ContentLayouts\ContentLayouts;
use Btybug\btybug\Models\Templates\Eloquent\Abstractions\TplModel;

class Units extends TplModel
{
    use autoinclude;

    public static $type = 'units';
    public $variationPath = 'variations';
    protected $tplpath = 'resources/units';
    protected $config = 'config.json';

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

    /**
     * @param $slug
     * @return mixed
     */
    public static function renderLivePreview($slug = NULL, $type = 'frontend')
    {
        $ui = $model = Units::findByVariation($slug);
        if (!$model) {
            return false;
        }
        $variation = self::findVariation($slug);
        $settings = (isset($variation->settings) && $variation->settings) ? $variation->settings : [];
        $body = url('/admin/uploads/gears/settings-iframe', $slug);
        $dataSettings = url('/admin/uploads/gears/settings-iframe', $slug) . '/settings';
        $data['body'] = $body;
        $data['settings'] = $dataSettings;
        return view('console::backend.gears.units.preview', compact(['model',"ui", 'id', 'data', 'settings', 'variation']));
    }

    public static function findUnit($slug)
    {
        $slug = explode('.', $slug);
        if (!isset($slug[0])) {
            return null;
        }
        return self::find($slug[0]);
    }

    public static function findVariation($id)
    {
        $slug = explode('.', $id);
        $variation = new UnitsVariations();
        $tpl = self::find($slug[0]);
        return $variation->findVarition($tpl, $id);
    }

    /**
     * @param $slug
     * @param null $title
     * @param $data
     * @param null $isSave
     * @return array|bool
     */
    public static function saveSettings($slug, $title = NULL, $data, $isSave = NULL)
    {
        if ($isSave && $isSave == 'save') {
            $unit = self::findUnit($slug);
            $existingVariation = self::findVariation($slug);
            $dataToInsert = [
                'title' => $title,
                'settings' => $data
            ];
            if (!$existingVariation) {
                $variation = new UnitsVariations();
                $variation = $variation->createVariation($unit, $dataToInsert);
            } else {
                $existingVariation->title = $title;
                $existingVariation->settings = $dataToInsert['settings'];
                $variation = $existingVariation;
            }
            if (!$variation->settings) {
                $variation->setAttributes('settings', []);
            }
            $settings = (isset($variation->settings) && $variation->settings) ? $variation->settings : [];
            if ($variation->save()) {
                //dd($variation->id);
                return [
                    'html' => $unit->renderLive(['settings' => $settings, 'source' => BBGiveMe('array', 5), 'cheked' => 1]),
                    'slug' => $variation->id
                ];
            }
        } else {
            return [
                'html' => self::findByVariation($slug)->renderLive(['settings' => $data]),
                'slug' => $slug
            ];
        }
        return false;
    }

    public static function findByVariation($id)
    {
        $slug = explode('.', $id);
        $variation = new UnitsVariations();

        $tpl = self::find($slug[0]);
        return $tpl;
    }

    public function variations()
    {
        return $this->allVars(UnitsVariations::class);
    }

    public function makeVariation($array)
    {
        $vars = new TplVariations();
        return $vars->createVariation($this, $array);
    }

    public function scopeMakeVariation(array $array = [])
    {
        $vars = new TplVariations();
        return $vars->createVariation($this, $array);
    }
    public function plugin()
    {
        $plugins = new Plugins();
        return $plugins->find($this->plugin);
    }

    public function delete(){
        $mainConfigFilePath = storage_path('app' . DS  . 'units.json');
        if (\File::exists($mainConfigFilePath)) {
            $mainConfigFile = \File::get(storage_path('app' . DS  . 'units.json'));
            $mainConfigFileDecoded = json_decode($mainConfigFile, true);
            if ($mainConfigFileDecoded) {
                if (array_key_exists($this->slug, $mainConfigFileDecoded)
                    && isset($mainConfigFileDecoded[$this->slug]['slug'])
                    && $mainConfigFileDecoded[$this->slug]['slug'] == $this->slug) {
                    unset($mainConfigFileDecoded[$this->slug]);
                    $editedMainConfigFileEncoded = json_encode($mainConfigFileDecoded);
                    if (\File::put($mainConfigFilePath, $editedMainConfigFileEncoded)) {
                        return \File::deleteDirectory($this->path);
                    }
                }
            }
        }
        return false;
    }

}