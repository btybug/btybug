<?php namespace Btybug\btybug\Models\Themes;

use File;
use Btybug\btybug\Models\ContentLayouts\autoinclude;

class Themes
{
    use autoinclude;
    /**
     * @var
     */
    public $path;
    /**
     * @var string
     */
    protected $dir;
    /**
     * @var string
     */
    protected $mdir;
    /**
     * @var
     */
    protected $attributes;

    /**
     * Modules constructor.
     */
    public function __construct()
    {
        $this->dir = base_path('resources/views/Themes');
        if (!File::exists(storage_path('app/themes.json'))) {
            File::put(storage_path('app/themes.json'), json_encode([], true));
        }
        $this->mdir = storage_path('app/themes.json');
    }

    /**
     * @return string
     */
    public static function defaultPageSection()
    {
        $active = self::active();
        return 'Themes.' . $active->folder . '.' . $active->layout;
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
     * @param $slug
     * @return mixed
     */
    public static function renderLivePreview($slug, $settings = [])
    {
        $variation = self::findVariation($slug);
        $data['view'] = "settings::backend_theme.pages_layout_settings";
        $data['request'] = $settings;
        if ($variation) {
            $data['variation'] = $variation;
            return self::findByVariation($slug)->renderSettings($data);
        } else if (self::find($slug)) {
            $variation = new ThemeVariations();
            $tpl = self::find($slug);
            $data['variation'] = $variation->createVariation($tpl, []);
            return self::find($slug)->renderSettings($data);
        }
    }

    /**
     * @param $id
     * @return null
     */
    public static function findVariation($id)
    {
        $slug = explode('.', $id);
        $variation = new ThemeVariations();
        $tpl = self::find($slug[0]);
        if ($tpl) {
            return $variation->findVarition($tpl, $id);
        } else {
            return null;
        }
    }

    /**
     * @param $id
     * @return null
     */
    public static function findByVariation($id)
    {
        $slug = explode('.', $id);
        $layout = self::find($slug[0]);
        if ($layout) {
            return $layout;
        } else {
            return null;
        }
    }

    /**
     * @param $slug
     * @param null $title
     * @param $data
     * @param null $isSave
     * @return array|bool
     */
    public static function savePageSectionSettings($slug, $title = NULL, $data, $isSave = NULL)
    {
        if ($isSave && $isSave == 'save') {
            $variation = new ContentLayoutVariations();
            $tpl = self::findByVariation($slug);
            $existingVariation = $variation->findVarition($tpl, $slug);
            $dataToInsert = [
                'title' => $title,
                'settings' => $data
            ];
            if (!$existingVariation) {
                $variation = new ContentLayoutVariations();
                if ($tpl->autoinclude) {
                    $variation = $tpl->makeAutoIncludeVariation(null, $dataToInsert);

                } else {
                    $variation = $variation->createVariation($tpl, $dataToInsert);
                }
            } else {
                $existingVariation->title = $title;
                $existingVariation->settings = $dataToInsert['settings'];
                $variation = $existingVariation;
            }
            if ($variation->save()) {
                return ['id' => $variation->id];
            }
        } else {
            return ['data' => self::findByVariation($slug)->renderLive($data)];
        }
        return false;
    }

    public static function deleteVariation($id)
    {
        $slug = explode('.', $id);
        $tpl = self::find($slug[0]);
        return ThemeVariations::delete($id, $tpl);
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
     * @return mixed
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     * @return array
     */
    public function setAttributes($key, $value)
    {
        $attributes = $this->attributes;
        $attributes[$key] = $value;
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return mixed|null
     */
    public function save()
    {
        $attributes = $this->attributes;
        $attributes = json_encode($attributes, true);
        File::put($this->path . '/config.json', $attributes);
        return $this;
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
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param $slug
     * @return mixed
     */
    public function getPluginLayouts($slug)
    {

        $json_data = json_decode(File::get($this->mdir), true);
        return $json_data;
    }

    public function scopeStyle($path)
    {
        return \Html::style('units/styles/' . $this->self_type . '/' . $this->slug . '/' . $path);
    }

    public function scopeScript($path)
    {
        return \Html::script('units/scripts/' . $this->self_type . '/' . $this->slug . '/' . $path);
    }

    protected function scopeMakeVariation($setteings = [])
    {
        $variation = new ThemeVariations();
        return $variation->createVariation($this, $setteings);
    }

    /**
     * @param $type
     * @return \Illuminate\Support\Collection
     */
    private function scopeFindByType($type)
    {
        $result = [];
        foreach ($this->scopeAll() as $plugin) {
            if ($plugin->type == $type) {
                $result[] = $plugin;
            }
        }
        return collect($result);

    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function scopeAll()
    {
        if (!File::isDirectory($this->dir)) File::makeDirectory($this->dir);
        $dirs = File::directories($this->dir);
        $plugins = array();

        if (!count($dirs)) return null;
        foreach ($dirs as $layoutDir) {
            if (File::exists($layoutDir . '/' . 'config.json')) {
                $attributes = @json_decode(File::get($layoutDir . '/' . 'config.json'), true);
                if ($attributes) {
                    $plugin = new $this;
                    $plugin->path = $layoutDir;
                    $plugin->attributes = $attributes;
                    $plugins[] = $plugin;
                }

            }
        }
        return collect($plugins);

    }

    /**
     * @param array $variables
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function scopeRenderSettings(array $variables = [], array $data = [])
    {
        $variation = $variables['variation'];
        $settings = $variation->toArray();
        if (isset($variables['request'])) {
            $settings = array_merge($variables['request'], $settings);
        }
        $slug = $this->folder;
        $layout = ($this->example) ? $this->example : $this->layout;
        $setting = isset($this->settings['file']) ? $this->settings['file'] : NULL;
        $json = json_encode($settings, true);
        $settingsHtml = "Themes.$slug.$setting";
        $model = $this;
        if ($this->autoinclude) {
            $html = $this->getAutoInclude()->getRender($settings, "Themes.$slug");
        } else {
            $html = \View::make("Themes.$slug.$layout")->with(['settings' => $settings, '_this' => $this])->render();
        }
        return view($variables['view'], compact(['model', 'settingsHtml', 'json', 'html', 'settings', 'data', 'variation']));
    }

    /**
     * @param array $variables
     * @return string
     */
    private function scopeRenderLive(array $variables = [])
    {
        $slug = $this->folder;
        $layout = ($this->example) ? $this->example : $this->layout;
        $html = \View::make("Themes.$slug.$layout")->with(['settings' => $variables])->render();
        return $html;
    }

    private function scopeRender(array $variables = [])
    {
        $slug = $this->folder;
        if ($this->autoinclude) return $this->getAutoInclude()->getRender($variables, "Themes.$slug");
        $html = \View::make("Themes.$slug.$this->layout")->with(['settings' => $variables, '_this' => $this])->render();
        return $html;
    }

    /**
     * @param $settings
     * @return mixed|null
     */
    private function scopeSaveSettings($settings)
    {
        $slug = $this->slug;
        $attributes = $this->attributes;
        $attributes['options'] = $settings;
        $attributes = json_encode($attributes, true);
        File::put($this->path . '/config.json', $attributes);
        return $this->scopeFind($slug);
    }

    /**
     * @param $slug
     * @return mixed|null
     */
    private function scopeFind($slug)
    {
        if (!$slug) return null;
        foreach ($this->scopeAll() as $plugin) {
            if ($plugin->slug == $slug) {
                return $plugin;
            }
        }
        return null;

    }

    /**
     * @param $key
     * @param null $value
     * @return array
     */
    private function scopePluck($key, $value = null)
    {

        $lusts = array();
        foreach ($this->scopeAll() as $template) {
            $lusts[$template->toArray()[$key]] = $template->toArray()[$value];
        }
        return $lusts;

    }

    /**
     * @return $this
     */
    private function scopeVariations()
    {
        $contentLayoutVariations = new ThemeVariations();
        return $contentLayoutVariations->findV($this->path);
    }

    /**
     * @param $slug
     * @return mixed|null
     */
    private function scopeActive()
    {
        foreach ($this->scopeAll() as $pageSection) {
            if ($pageSection->active) {
                return $pageSection;
            }
        }
        return null;
    }

    private function scopeMakeInActive()
    {
        $data = $this->attributes;
        $data['active'] = false;
        $this->attributes = $data;
        return $this;
    }

    /**
     * @param $slug
     * @return mixed|null
     */
    private function scopeActiveVariation($slug = null)
    {
        if (!$slug) {
            $slug = $this->slug;
        }
        foreach (self::findByVariation($slug)->variations() as $variation) {
            if ($variation->active) {
                return $variation;
            }
        }
        return null;
    }
}