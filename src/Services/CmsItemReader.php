<?php
/**
 * Created by PhpStorm.
 * User: Ara Arakelyan
 * Date: 1/25/2017
 * Time: 5:36 PM
 */

namespace Btybug\btybug\Services;

use File;
use Btybug\btybug\Models\Templates\Eloquent\Abstractions\TplModel;
use Btybug\btybug\Models\Templates\UnitsVariations;

class CmsItemReader extends TplModel
{

    public $variationPath = 'variations';

    public function __construct()
    {
        parent::__construct();
        $this->config = config('config.MODULE_CONFIG_FILE_NAME');
    }

    public static function getModuleGearsBySlug($slug)
    {
        if ($slug) {
            $templates = new static;
            $configFileData = self::getRegisteredDataFromFiles();
            if ($configFileData) {
                foreach ($configFileData as $key => $currentGear) {
                    if (self::checkGearInFileStructureWithPath($currentGear['path'])) {
                        if (isset($currentGear['module_slug']) && $currentGear['module_slug'] == $slug) {
                            $tpl = new static;
//                            $conf = base_path($currentGear['path']) . '/' . $tpl->config;
                            $tpl->original = $tpl->attributes = $currentGear;
                            $tpl->path = ($currentGear['path']);
                            $tpl->folders[] = $tpl->main_type . DS . $tpl->type . DS . $tpl->slug;
                            $tpl->folders[] = $tpl->type . DS . $tpl->slug;
                            $templates->before[] = $tpl;
                        }
                    }
                }
            }
            return $templates;
        }
    }

    public static function getModuleBackendGearsBySlug($slug)
    {
        if ($slug) {
            $templates = new static;
            $configFileData = self::getRegisteredDataFromFiles();
            if ($configFileData) {
                foreach ($configFileData as $key => $currentGear) {
                    if (self::checkGearInFileStructureWithPath($currentGear['path'])) {
                        if (isset($currentGear['module_slug']) && $currentGear['module_slug'] == $slug && $currentGear['place'] == 'backend') {
                            $tpl = new static;
//                            $conf = base_path($currentGear['path']) . '/' . $tpl->config;
                            $tpl->original = $tpl->attributes = $currentGear;
                            $tpl->path = ($currentGear['path']);
                            $tpl->folders[] = $tpl->main_type . DS . $tpl->type . DS . $tpl->slug;
                            $tpl->folders[] = $tpl->type . DS . $tpl->slug;
                            $templates->before[] = $tpl;
                        }
                    }
                }
            }
            return $templates;
        }
    }

    public static function getAllGearsByType()
    {
        $_instance = new static();
        return $_instance->getAll();
    }



    public function deleteGear()
    {
        if ($this && $this->self_type) {
            $mainConfigFilePath = storage_path('app' . DS . strtolower($this->self_type) . '.json');
            if (File::exists($mainConfigFilePath)) {
                $mainConfigFile = File::get(storage_path('app' . DS . strtolower($this->self_type) . '.json'));
                $mainConfigFileDecoded = json_decode($mainConfigFile, true);
                if ($mainConfigFileDecoded) {
                    if (array_key_exists($this->slug, $mainConfigFileDecoded)
                        && isset($mainConfigFileDecoded[$this->slug]['slug'])
                        && $mainConfigFileDecoded[$this->slug]['slug'] == $this->slug
                        && !$mainConfigFileDecoded[$this->slug]['is_core']) {
                        unset($mainConfigFileDecoded[$this->slug]);
                        $editedMainConfigFileEncoded = json_encode($mainConfigFileDecoded);
                        if (File::put($mainConfigFilePath, $editedMainConfigFileEncoded) && File::exists(($this->path))) {
                            return File::deleteDirectory(($this->path));
                        }
                    }
                }
            }
        }
        return false;
    }

    public function findVariationByGear($slug)
    {
        $variations = $this->variations();

        if (count($variations)) {
            foreach ($variations as $variation) {
                if ($slug == $variation->id) {
                    return $variation;
                }
            }
        }
        return [];
    }

    public function variations()
    {
        return $this->allVars(UnitsVariations::class);
    }


}