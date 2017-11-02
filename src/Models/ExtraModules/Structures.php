<?php
/**
 * Created by PhpStorm.
 * User: Comp1
 * Date: 12/26/2016
 * Time: 1:42 PM
 */

namespace Btybug\btybug\Models\ExtraModules;

/**
 * Class Structures
 * @package App\Models\ExtraModules
 */
class Structures
{
    /**
     * @var
     */
    protected $attributes;

    /**
     * @var
     */
    private $type;
    /**
     * @var string
     */
    private $bbJson = DS . 'bb.json';

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $method = 'scope' . ucfirst($name);
        $_this = new static;
        if (method_exists($_this, $method)
            && is_callable(array($_this, $method))
        ) {
            return call_user_func_array([$_this, 'scope' . ucfirst($name)], $arguments);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function __get($name)
    {
        $result = isset($this->toArray()[$name]) ? $this->toArray()[$name] : false;
        return $result;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function __set($name, $value)
    {
        if (isset($this->attributes[$name])) {
            $this->attributes[$name] = $value;
            return $this;
            // TODO: Implement __set() method.
        }
    }

    /**
     * @return bool
     */
    public function toArray()
    {
        if (isset($this->attributes)) return $this->attributes;
        return false;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $method = 'scope' . ucfirst($name);
        if (method_exists($this, $method)
            && is_callable(array($this, $method))
        ) {
            return call_user_func_array([$this, 'scope' . ucfirst($name)], $arguments);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        $result = isset($this->toArray()[$name]) ? true : false;
        return $result;
    }

    /**
     * @return null
     */
    public function getType()
    {
        $result = isset($this->toArray()['type']) ? $this->toArray()['type'] : null;
        return $result;
    }

    /**
     * @param null $slug
     * @return \Illuminate\Support\Collection|null|static
     */
    private function scopeGetExtraModules($slug = null)
    {
        $config = new config();
        $colect = [];
        if ($slug) {
            if (isset($config->plugins[$slug])) {
                if (isset($config->plugins[$slug]['type']) and $config->plugins[$slug]['type'] == "extra") {
                    $_this = new static();
                    $_this->type = 'extra';
                    $_this->attributes = $config->plugins[$slug];
                    return $_this;
                }
            }
            return null;
        }
        foreach ($config->plugins as $k => $v) {
            if (isset($v['type']) and $v['type'] == "extra") {
                $_this = new static();
                $_this->type = 'extra';
                $_this->attributes = $v;
                $colect[$_this->namespace] = $_this;
            }
        }
        return collect($colect);
    }

    private function scopeGetBuilderModules($slug = null)
    {
        $config = new config();
        $colect = [];
        if ($slug) {
            if (isset($config->plugins[$slug])) {
                if (isset($config->plugins[$slug]['type']) and $config->plugins[$slug]['type'] == "extra" && isset($config->plugins[$slug]['builder'])) {
                    $_this = new static();
                    $_this->type = 'extra';
                    $_this->attributes = $config->plugins[$slug];
                    return $_this;
                }
            }
            return null;
        }
        foreach ($config->plugins as $k => $v) {
            if (isset($v['type']) and $v['type'] == "extra" and isset($v['builder'])) {
                $_this = new static();
                $_this->type = 'extra';
                $_this->attributes = $v;
                $colect[$_this->namespace] = $_this;
            }
        }
        return collect($colect);
    }

    /**
     * @param $slug
     * @return Structures
     */
    private function scopeFind($slug)
    {
        return $this->scopeGetStructure($slug);
    }

    /**
     * @param null $slug
     * @return static
     */
    private function scopeGetStructure($slug = null)
    {
        $config = new config();
        if ($slug) {
            if (isset($config->addons[$slug])) {
                dd($config->addons[$slug]);
                $_this = new static();
                $_this->type = $config->addons[$slug]['type'];
                $_this->attributes = $config->addons[$slug];
                return $_this;
            }
            if (isset($config->plugins[$slug])) {
                $_this = new static();
                $_this->type = $config->plugins[$slug]['type'];
                $_this->attributes = $config->plugins[$slug];
                return $_this;
            }
            if (isset($config->modules[ucfirst($slug)])) {
                $_this = new static();
                $_this->type = $config->modules[ucfirst($slug)]['type'];
                $_this->attributes = $config->modules[ucfirst($slug)];
                return $_this;
            }

            if (isset($config->extras[ucfirst($slug)])) {
                $_this = new static();
                $_this->type = $config->extras[ucfirst($slug)]['type'];
                $_this->attributes = $config->extras[ucfirst($slug)];
                return $_this;
            }

        }
    }

    /**
     * @return null
     */
    private function scopeDelete()
    {
        $keys = explode('_', $this->slug);
        switch ($keys[0]) {
            case 'addon':

                break;
            case 'plugin':

                break;
            case 'extra':

                break;
            default:
                return null;
        }
    }

    /**
     * @param $type
     * @param $key
     * @return int
     */
    private function unsets($type, $key)
    {
        $config = new config();
        $path = $config->jsons[$type];
        $json = $config->{$type};
        $structure = $json[$key];
        if (\File::isDirectory($structure['path'])) {
            \File::deleteDirectory($structure['path']);
        }
        unset($json[$key]);
        return \File::put($path, json_encode($json, true));
    }

    /**
     * @param array $data
     * @return mixed
     */
    private function scopeCreate(array $data)
    {
        $data['enabled'] = false;
        $_this = new static();
        $_this->type = $data['type'];
        $_this->attributes = $data;
        return $_this->save();
    }

    /**
     * @return bool
     */
    private function scopeEnable()
    {
        $this->enabled = true;
        if ($this->scopeSave()) return true;
        return false;
    }

    /**
     * @return int
     */
    private function scopeSave()
    {
        $type = $this->type . 's';
        $config = new config();
        $path = $config->jsons[$type];
        $json = $config->{$type};
        $json[$this->slug] = $this->toArray();
        return \File::put($path, json_encode($json, true));
    }

    /**
     * @return bool
     */
    private function scopeDisable()
    {
        $this->enabled = false;
        if ($this->scopeSave()) return true;
        return false;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function scopeGetActivePlugins()
    {
        $plugins = $this->scopeGetPlugins();
        $collect = [];
        foreach ($plugins as $plugin) {
            if ($plugin->enabled) {
                $collect[] = $plugin;
            }
        }
        return collect($collect);
    }

    /**
     * @param null $slug
     * @param null $is_active
     * @return \Illuminate\Support\Collection
     */
    private function scopeGetPlugins($slug = null, $is_active = null)
    {
        $config = new config();
        $colect = [];
        foreach ($config->plugins as $k => $v) {
            $_this = new static();
            $_this->type = 'plugin';
            $_this->attributes = $v;
            if ($is_active) {
                if ($_this->enabled == true) {
                    $colect[] = $_this;
                }
            } else {
                $colect[] = $_this;
            }


        }
        return collect($colect);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function scopeGetActiveAddons()
    {
        $plugins = $this->scopeGetAddons();
        $collect = [];
        foreach ($plugins as $plugin) {
            if ($plugin->enabled) {
                $collect[] = $plugin;
            }
        }
        return collect($collect);
    }

    /**
     * @param null $slug
     * @return \Illuminate\Support\Collection
     */
    private function scopeGetAddons($slug = null)
    {
        $config = new config();
        $colect = [];
        foreach ($config->addons as $k => $v) {
            if (isset($v['module']) and $v['module'] == $slug) {
                $_this = new static();
                $_this->type = 'addon';
                $_this->attributes = $v;
                $colect[] = $_this;
            }
        }
        return collect($colect);
    }

    /**
     *
     */
    private function scopeChilds()
    {

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function scopeGetAllBbjsons()
    {
        $plugins = $this->scopeGetAll('plugin');
        $modules = $this->scopeGetAll('module');
        $addons = $this->scopeGetAll('addon');
        $result['plugins'] = $this->makeBBjson($plugins);
        $result['modules'] = $this->makeBBjson($modules);
        $result['addons'] = $this->makeBBjson($addons);
        $result['core'] = [json_decode(\File::get(base_path('app' . DS . 'Core' . DS . 'filter' . $this->bbJson)), true)];

        return collect($result);

    }

    /**
     * @param $type
     * @return \Illuminate\Support\Collection
     */
    private function scopeGetAll($type)
    {
        $type = $type . 's';
        $config = new config();
        $path = $config->jsons[$type];
        $json = $config->{$type};
        $colect = [];
        foreach ($json as $k => $v) {
            $_this = new static();
            $_this->type = $v['type'];
            $_this->attributes = $v;
            $colect[] = $_this;

        }
        return collect($colect);
    }

    /**
     * @param $structurejson
     * @return array
     */
    private function makeBBjson($structurejson)
    {
        $colection = [];
        foreach ($structurejson as $structure) {
            if ($structure->type == 'core') {
                if (\File::exists(base_path('app' . DS . 'Modules' . DS . $structure->basename . $this->bbJson))) {
                    $colection[$structure->slug] = json_decode(\File::get(base_path('app' . DS . 'Modules' . DS . $structure->basename . $this->bbJson)), true);
                }
            } else {
                if (\File::exists(base_path($structure->getPath() . $this->bbJson))) {
                    $colection[$structure->slug] = json_decode(\File::get(base_path($structure->getPath() . $this->bbJson)), true);
                }
            }
        }
        return $colection;
    }

    /**
     * @return mixed|null
     */
    private function scopeGetBBjson()
    {
        $result = null;
        if ($this->type == 'core') {
            if (\File::exists(base_path('app' . DS . 'Modules' . DS . $this->basename . $this->bbJson))) {
                $result = json_decode(\File::get(base_path('app' . DS . 'Modules' . DS . $this->basename . $this->bbJson)), true);
            }
        } else {
            if (\File::exists(base_path($this->getPath() . $this->bbJson))) {
                $result = json_decode(\File::get(base_path($this->getPath() . $this->bbJson)), true);
            }
        }
        return $result;

    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

}