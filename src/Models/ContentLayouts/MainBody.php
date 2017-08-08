<?php namespace Sahakavatar\Cms\Models\ContentLayouts;

use Sahakavatar\Cms\Services\CmsItemReader;
use File;
use Sahakavatar\Cms\Models\ContentLayouts\ContentLayoutVariations;

/**
 * Class ContentLayouts
 * @package Sahakavatar\Cms\Models\ContentLayouts
 */
class MainBody
{
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
    public $path;
    /**
     * @var
     */
    protected $attributes;

    /**
     * Modules constructor.
     */
    public function __construct()
    {
        $this->dir = base_path('resources/views/ContentLayouts');
        if (!File::exists(storage_path('app/main_body.json'))) {
            File::put(storage_path('app/main_body.json'), json_encode([], true));
        }
        $this->mdir = storage_path('app/main_body.json');
    }

    /**
     * @return string
     */
    public static function defaultPageSection()
    {
        $active = self::active();
        return 'ContentLayouts.' . $active->folder . '.' . $active->layout;
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
     * @param $slug
     * @return \Illuminate\Support\Collection|null
     */

    /**
     * @return \Illuminate\Support\Collection
     */

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
     * @param array $variables
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function scopeRenderSettings(array $variables = [], array $data = [])
    {
        $variation = $variables['variation'];
        $slug = $this->folder;
        $layout = ($this->example) ? $this->example : $this->layout;
        $setting = isset($this->settings['file']) ? $this->settings['file'] : NULL;
        $json = json_encode($variation->settings, true);
        $settingsHtml = "ContentLayouts.$slug.$setting";
        $model = $this;
        $html = \View::make("ContentLayouts.$slug.$layout")->with(['settings' => $this->options])->render();

        if (isset($data['page'])) {
            $page = $data['page'];
            if ($page->settings && ! $data['isLivePreview']) {
                $settings = json_decode($page->settings, true);
            } else {
                $settings = $variation->settings;
                $html = \View::make("ContentLayouts.$slug.$layout")->with(['settings' => $settings])->render();
            }
        } else {
            $settings = $variation->settings;
            $html = \View::make("ContentLayouts.$slug.$layout")->with(['settings' => $settings])->render();
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
        $html = \View::make("ContentLayouts.$slug.$layout")->with(['settings' => $variables])->render();
        return $html;
    }

    private function scopeRender(array $variables = [])
    {
        $slug = $this->folder;
        $html = \View::make("ContentLayouts.$slug.$this->layout")->with(['settings' => $variables])->render();
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
     * @return bool
     */
    public function toArray()
    {
        if (isset($this->attributes)) return $this->attributes;
        return false;
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
     * @return $this
     */
    private function scopeVariations()
    {
        $contentLayoutVariations = new ContentLayoutVariations();
        return $contentLayoutVariations->findV($this->path);
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

    /**
     * @param $id
     * @return null
     */
    public static function findVariation($id)
    {
        $slug = explode('.', $id);
        $variation = new ContentLayoutVariations();

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
        $variation = new ContentLayoutVariations();
        $layout = self::find($slug[0]);
        if ($layout) {
            return $layout;
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
                $variation = $variation->createVariation($tpl, $dataToInsert);
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
        return ContentLayoutVariations::delete($id, $tpl);
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