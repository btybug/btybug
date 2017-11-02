<?php

namespace Btybug\btybug\Models\ExtraModules;

/**
 * User: Sahak
 * Date: 8/8/2016
 * Time: 2:47 PM
 */
use File;

/**
 * Class Modules
 * @package App\Models\ExtraModules
 */
class Modules
{
    /**
     * @var string
     */
    public $dir;
    /**
     * @var
     */
    protected $attributes;
    /**
     * @var
     */
    private $path;

    /**
     * Modules constructor.
     */
    public function __construct()
    {
        $this->dir = base_path('app/ExtraModules');
    }

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
     * @return array|bool
     */
    public function deleteWithAddons()
    {
        $addons = $this->scopeAddons();
        foreach ($addons as $addon) {
            $addon->delete();
        }
        return $this->delete();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function scopeAddons()
    {
        $slug = $this->slug;
        $this->dir = base_path('app/Modules');
        $dirs = File::directories($this->dir . '/' . camel_case($slug) . '/Plugins');

        $plugins = array();
        foreach ($dirs as $pluginDir) {
            if (File::exists($pluginDir . '/' . 'module.json')) {
                $attributes = @json_decode(File::get($pluginDir . '/' . 'module.json'), true);
                if ($attributes and isset($attributes['type']) and isset($attributes['module'])) {
                    if ($attributes['type'] == 'addon' and $attributes['module'] == $slug and $attributes['enabled'] == true) {
                        $plugin = new $this;
                        $plugin->path = $pluginDir;
                        $plugin->attributes = $attributes;
                        $plugins[] = $plugin;
                    }

                }

            }
        }
        return collect($plugins);
    }

    /**
     * @return array|bool
     */
    public function delete()
    {
        if (isset($this->autoload)) {
            $autoloadClass = 'App\ExtraModules\\' . $this->namespace . '\\' . $this->autoload;

            if (class_exists($autoloadClass)) {
                $autoload = new $autoloadClass();
                try {
                    $autoload->down();
                } catch (\Exception $e) {
                    return ['message' => $e->getMessage(), 'code' => $e->getCode(), 'error' => true];
                }

            }
        }

        $forms = $this->forms;
        if ($forms && !empty($forms)) {
            PluginForms::removeRecursive($forms);
        }

        return File::deleteDirectory($this->path);
    }

    /**
     * @return $this
     */
    public function enable()
    {
        $data = $this->toArray();
        $data['enabled'] = true;
        File::put($this->path . '/' . 'module.json', json_encode($data, true));
        return $this;

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
     * @return Modules
     */
    public function disableWithAddons()
    {
        $addons = $this->scopeAddons();
        foreach ($addons as $addon) {
            $addon->disable();
        }
        return $this->disable();
    }

    /**
     * @return $this
     */
    public function disable()
    {
        $data = $this->toArray();
        $data['enabled'] = false;
        File::put($this->path . '/' . 'module.json', json_encode($data, true));
        return $this;
    }

    /**
     * @param $namespace
     * @return $this|null
     */
    public function scopeFindByNamespace($namespace)
    {
        $pluginDir = $this->dir . '/' . $namespace;
        if (File::exists($pluginDir . '/' . 'module.json')) {
            $attributes = @json_decode(File::get($pluginDir . '/' . 'module.json'), true);
            if ($attributes and isset($attributes['type'])) {
                $this->path = $pluginDir;
                $this->attributes = $attributes;
                return $this;
            }
        }
        return null;
    }

    /**
     * @return mixed|null
     */
    public function parent()
    {
        if (isset($this->module)) return $this->scopeFind($this->module);
        return null;
    }

    /**
     * @param $slug
     * @return mixed|null
     */
    private function scopeFind($slug)
    {
        if (!$slug) return null;
        foreach ($this->scopeAllModules() as $plugin) {
            if ($plugin->slug == $slug) {
                return $plugin;
            }
        }
        return null;

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function scopeAllModules()
    {
        $dirs = File::directories($this->dir);
        $plugins = array();

        if (!empty($dirs)) return null;

        foreach ($dirs as $pluginDir) {
            if (File::exists($pluginDir . '/' . 'module.json')) {
                $attributes = @json_decode(File::get($pluginDir . '/' . 'module.json'), true);
                if ($attributes and isset($attributes['type'])) {
                    if ($attributes['type'] == 'module') {
                        $plugin = new $this;
                        $plugin->path = $pluginDir;
                        $plugin->attributes = $attributes;
                        $plugins[] = $plugin;
                    }

                }

            }
        }
        return collect($plugins);

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
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param $slug
     * @return \Illuminate\Support\Collection|null
     */
    private function scopeGetModuleAddons($slug)
    {
        if (!$slug) return null;

        $this->dir = base_path('app/Modules');
        $plugins = array();
        if (File::isDirectory($this->dir . '/' . camel_case($slug) . '/Plugins')) {
            $dirs = File::directories($this->dir . '/' . camel_case($slug) . '/Plugins');
            foreach ($dirs as $pluginDir) {
                if (File::exists($pluginDir . '/' . 'module.json')) {
                    $attributes = @json_decode(File::get($pluginDir . '/' . 'module.json'), true);
                    if ($attributes and isset($attributes['type']) and isset($attributes['module'])) {
                        if ($attributes['type'] == 'addon' and $attributes['module'] == $slug) {
                            $plugin = new $this;
                            $plugin->path = $pluginDir;
                            $plugin->attributes = $attributes;
                            $plugins[] = $plugin;
                        }

                    }

                }
            }
        }

        return collect($plugins);
    }

    private function scopeFindAddon($slug)
    {
        if (!$slug) return null;
        foreach ($this->scopeAllAddons() as $addon) {
            if ($addon->slug == $slug) {
                return $addon;
            }
        }
        return null;

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function scopeAllAddons()
    {
        $dirs = File::directories($this->dir);
        $plugins = array();
        foreach ($dirs as $pluginDir) {
            if (File::exists($pluginDir . '/' . 'module.json')) {
                $attributes = @json_decode(File::get($pluginDir . '/' . 'module.json'), true);
                if ($attributes and isset($attributes['type'])) {
                    if ($attributes['type'] == 'addon') {
                        $plugin = new $this;
                        $plugin->path = $pluginDir;
                        $plugin->attributes = $attributes;
                        $plugins[] = $plugin;
                    }

                }

            }
        }
        return collect($plugins);
    }
}