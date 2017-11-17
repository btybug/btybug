<?php namespace Btybug\btybug\Models\ContentLayouts;

use File;
use Btybug\btybug\Models\Hook;
use Btybug\btybug\Repositories\AdminsettingRepository;

/**
 * Class ContentLayouts
 * @package Btybug\btybug\Models\ContentLayouts
 */
class ContentLayouts
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
        $this->dir = base_path('resources/views/ContentLayouts');
        if (!File::exists(storage_path('app/content_layout.json'))) {
            File::put(storage_path('app/content_layouts.json'), json_encode([], true));
        }
        $this->mdir = storage_path('app/content_layout.json');
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
     * @return \Illuminate\Support\Collection
     */

    public static function renderPageBody($id, $settings = [])
    {
        if (File::exists(storage_path('pages_body' . DS . $id . '.json'))) {
            $file = json_decode(File::get(storage_path('pages_body' . DS . $id . '.json')), true);
            $body = self::findByVariation($file['id']);
            $settings = array_merge($settings, $file);
            if ($body) {
                return $body->render($settings);
            }
        }
        return null;
    }

    /**
     * @param $slug
     * @return \Illuminate\Support\Collection|null
     */

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
     * @return mixed
     */
    public static function renderLivePreview($slug, $settings = [])
    {
        $variation = self::findVariation($slug);
        $data['view'] = "uploads::gears.page_sections.live_preview.settings";
        $data['request'] = $settings;
        if ($variation) {
            $data['variation'] = $variation;
            return self::findByVariation($slug)->renderSettings($data);
        } else if (self::find($slug)) {
            $data['variation'] = $variation;
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
        $variation = new ContentLayoutVariations();
        $tpl = self::find($slug[0]);
        if ($tpl) {
            return $variation->findVarition($tpl, $id);
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
                $existingVariation->setAttributes('title', $title);
                $existingVariation->setAttributes('settings', $dataToInsert['settings']);
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

    public function scopeSortByTag($tag)
    {

        foreach ($this->scopeAll() as $item) {
            if (isset($item->tags) && array_search($tag, $item->tags) > -1) {
                $result[] = $item;
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
     * @return array|bool
     */
    public function delete()
    {
        $mainConfigFilePath = storage_path('app' . DS  . 'page_sections.json');
        if (\File::exists($mainConfigFilePath)) {
            $mainConfigFile = \File::get(storage_path('app' . DS  . 'page_sections.json'));
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

    public function addTag($tag)
    {
        $this->tags[] = $tag;
        return $this;
    }

    public function removeTag($tag)
    {
        if (is_array($this->tags)) {
            $index = array_search($tag, $this->tags);
            if ($index) {
                unset($this->tags[$index]);
            }
        }

    }

    protected function scopeMakeVariation($setteings = [])
    {
        $variation = new ContentLayoutVariations();
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
     * @param array $variables
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    private function scopeRenderSettings(array $variables = [], array $data = [])
    {
        $variation = $variables['variation'];
        $settings = ($variation) ? $variation->toArray() : $variation;
        if($settings){
            if (isset($variables['request'])) {
                $settings = array_merge($variables['request'], $settings['settings']);
            } else {
                $settings = $settings['settings'];
            }
        }

        $slug = $this->folder;
        $layout = ($this->example) ? $this->example : $this->layout;
        $setting = isset($this->settings['file']) ? $this->settings['file'] : NULL;
        $json = json_encode($settings, true);
        $settingsHtml = "ContentLayouts.$slug.$setting";
        $model = $this;
        if ($this->autoinclude) {
            $html = $this->getAutoInclude()->getRender($settings, "ContentLayouts.$slug");
        } else {
            $html = \View::make("ContentLayouts.$slug.$layout")->with(['settings' => $settings, '_this' => $this])->render();
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
        $html = \View::make("ContentLayouts.$slug.$layout")->with(['settings' => $variables,'_this'=>$this])->render();
        return $html;
    }

    private function scopeRender(array $variables = [])
    {
        $slug = $this->folder;
        if ($this->autoinclude) return $this->getAutoInclude()->getRender($variables, "ContentLayouts.$slug");
        $html = \View::make("ContentLayouts.$slug.$this->layout")->with(['settings' => $variables, '_this' => $this])->render();
        return $html;
    }

    private function scopeRenderPagePanel($page)
    {
        $slug = $this->folder;
        if ($this->autoinclude) return $this->getAutoInclude()->getRender($page, "ContentLayouts.$slug");
        if (!\View::exists("ContentLayouts.$slug.$this->panel")) return "View not found";
        $html = \View::make("ContentLayouts.$slug.$this->panel")->with(['page' => $page, '_this' => $this])->render();
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
        $contentLayoutVariations = new ContentLayoutVariations();
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

    private function scopeGetPageLayout($page, $hide_top = false)
    {
        $layout = self::findByVariation($page->page_layout);
        if ($layout) {
            return $layout->getPageLayoutWidget($page, $hide_top);
        }
        return $this->getPageLayoutWidget($page, $hide_top);

    }

    private function scopeGetAdminPageLayout($page, $hide_top = false)
    {
        $layout = self::findByVariation($page->layout_id);
        if ($layout) {
            return $layout->getAdminPageLayoutWidget($page, $hide_top);
        }
        return $this->getAdminPageLayoutWidget($page, $hide_top);

    }

    private function scopeGetPageLayoutPlaceholders($page, $hide_top = false)
    {
        $layout = self::findByVariation($page->page_layout);
        if ($layout) {
            return $layout->getPagePlaceholders($page, $hide_top);
        }
        return $this->getPagePlaceholders($page, $hide_top);
    }

    protected function getPagePlaceholders($page, $hide_top = false)
    {
        $_this = $this;
        return \View::make('btybug::_partials.placeholders', compact(['_this', 'page', 'hide_top']))->render();
    }
    protected function getPageLayoutWidget($page, $hide_top = false)
    {
        $_this = null;
        if (isset($this->placeholders)) {
            $_this = $this;
        }
        return \View::make('btybug::_partials.layout', compact(['_this', 'page', 'hide_top']))->render();
    }

    protected function getAdminPageLayoutWidget($page, $hide_top = false)
    {
        $_this = null;
        if (isset($this->placeholders)) {
            $_this = $this;
        }
        return \View::make('btybug::_partials.core_layout', compact(['_this', 'page', 'hide_top']))->render();
    }

    private function scopeGetDefaultLayoutPlaceholders($system)
    {
        if (!isset($system['page_layout'])) return $this->getDefaultPlaceholders();
        $layout = self::findByVariation($system['page_layout']);
        if ($layout) {
            if (isset($system['placeholders'])) {
                $system['placeholders'] = json_decode($system['placeholders'], true);
            }
//            dd($system);
            return $layout->getDefaultPlaceholders($system);
        }

        return $this->getDefaultPlaceholders();
    }

    protected function getDefaultPlaceholders($system = null)
    {
        $_this = null;
        if (isset($this->placeholders)) {
            $_this = $this;
        }
        return \View::make('btybug::_partials.default_placeholders', compact('_this', 'system'))->render();
    }

    private function scopeGetBackendDefaultLayoutPlaceholders($system)
    {
        if (!isset($system['backend_page_section'])) return $this->getBackendDefaultPlaceholders();
        $layout = self::findByVariation($system['backend_page_section']);
        if ($layout) {
            return $layout->getBackendDefaultPlaceholders($system);
        }

        return $this->getBackendDefaultPlaceholders();
    }

    protected function getBackendDefaultPlaceholders($system = null)
    {
        $_this = null;
        if (isset($this->placeholders)) {
            $_this = $this;
        }
        return \View::make('btybug::_partials.backend_default_placeholders', compact('_this', 'system'))->render();
    }

    private function scopeGetAdminPageLayoutPlaceholders($page,$hide_top = false)
    {
        $layout = self::findByVariation($page->layout_id);
        if ($layout) {
            return $layout->getAdminPagePlaceholders($page, $hide_top);
        }
        return $this->getAdminPagePlaceholders($page, $hide_top);
    }

    protected function getAdminPagePlaceholders($page = null)
    {
        $_this = $this;
        return \View::make('btybug::_partials.admin_placeholders', compact(['_this', 'page', 'hide_top']))->render();
    }

    public function scopeGetPageLayoutHooks($page)
    {
        $layout = self::findVariation($page->page_layout);
        if ($layout) {
                $html = \View::make('btybug::_partials.page_hooks', compact('layout'));
                return $html;
        }
    }

}