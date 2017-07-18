<?php
namespace Sahakavatar\Cms\Models\Templates;

use App\Models\Templates\Eloquent\Abstractions\TplModel;
use App\Models\Templates\TplVariations;
use App\Modules\Settings\Models\TemplateVariations;

class Templates extends TplModel
{

    /**
     * @var string
     */
    protected $tplpath = 'resources/templates';

    /**
     * @var string
     */
    protected $config = 'config.json';

    /**
     * @var string
     */
    public $variationPath = 'variations';

    public static $type = 'hf';

    /**
     * @return mixed
     */
    public function section()
    {
        return $this->belongsTo('App\Modules\Sections\Sections', 'section_id');
    }

    /**
     * @return mixed
     */
    public function variations()
    {
        return $this->allVars('App\Models\Templates\TplVariations');
    }

    public function makeVariation($array)
    {
        $vars = new TplVariations();
        if(isset($array['_token']))unset($array['_token']);
        $variations = $this->variations()->where('main', 1)->first();
        $array['settings']=$variations->settings;
        return $vars->createVariation($this, $array); // TODO: Change the autogenerated stub
    }

    public static function deleteVariation($id)
    {
        $slug = explode('.', $id);
        $tpl = self::find($slug[0]);

        return TplVariations::delete($id, $tpl);
    }

    public static function findVariation($id)
    {
//        $variation
        $slug = explode('.', $id);
        $variation = new TplVariations();
        $tpl = self::find($slug[0]);
        if ($tpl) {
            return $variation->findVarition($tpl, $id);
        } else {
            return null;
        }
    }


    /**
     * @param $slug
     * @return mixed
     */
    public static function renderLivePreview($slug)
    {
        $variation = self::findVariation($slug);
        $data['view'] = "settings::backend_theme.pages_layout_settings";
        if ($variation) {
            $data['variation'] = $variation;
            return self::findByVariation($slug)->renderSettings($data);
        } else if (self::find($slug)) {
            $variation = new ContentLayoutVariations();
            $tpl = self::find($slug);
            if ($tpl) {
                $data['variation'] = $variation->createVariation($tpl, []);
            }
            return self::find($slug)->renderSettings($data);
        }
    }

    public static function findHF($slug)
    {
        $slug = explode('.', $slug);
        if (!isset($slug[0])) {
            return null;
        }
        return self::find($slug[0]);
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
            $hf = self::findHF($slug);
            $existingVariation = self::findVariation($slug);
            $dataToInsert = [
                'title' => $title,
                'settings' => $data
            ];
            if (!$existingVariation) {
                $variation = new TplVariations();
                $variation = $variation->createVariation($hf, $dataToInsert);
            } else {
                $existingVariation->title = $title;
                $existingVariation->settings = $dataToInsert['settings'];
                $variation = $existingVariation;
            }
            if(!$variation->settings) {
                $variation->setAttributes('settings', []);
            }
            $settings = (isset($variation->settings) && $variation->settings) ? $variation->settings : [];

            if ($variation->save()) {
                return [
                    'html' => $hf->render(['settings' => $settings, 'source' => BBGiveMe('array', 5), 'cheked' => 1]),
                    'slug' => $variation->id
                ];
            }
        } else {
            $hf = self::findHF($slug);
            return [
                'html' => $hf->render(['settings' => $data]),
                'slug' => $slug
            ];
        }
        return false;
    }


}