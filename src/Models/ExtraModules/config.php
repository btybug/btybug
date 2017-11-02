<?php
/**
 * Created by PhpStorm.
 * User: Comp1
 * Date: 12/26/2016
 * Time: 1:54 PM
 */

namespace Btybug\btybug\Models\ExtraModules;

use File;

class config
{
    public $jsons;

    public $modules;
    public $plugins;
    public $addons;
    public $extras;

    public function __construct()
    {
        $this->jsons['modules'] = storage_path('app/modules.json');
        $this->jsons['plugins'] = storage_path('app/plugins.json');
        $this->jsons['extras'] = storage_path('app/plugins.json');
        $this->jsons['addons'] = storage_path('app/addons.json');
        foreach ($this->jsons as $path) {
            if (!File::exists($path)) {
                File::put($path, json_encode([], true));
            }
        }
        $this->modules = json_decode(File::get($this->jsons['modules']), true);
        $this->plugins = json_decode(File::get($this->jsons['plugins']), true);
        $this->extras = json_decode(File::get($this->jsons['plugins']), true);
        $this->addons = json_decode(File::get($this->jsons['addons']), true);
    }
}