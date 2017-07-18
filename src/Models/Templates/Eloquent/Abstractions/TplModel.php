<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 7/7/2016
 * Time: 8:55 PM
 */

namespace Sahakavatar\Cms\Models\Templates\Eloquent\Abstractions;

use App\Models\Templates\Eloquent\Interfaces\TplInterface;
use App\Modules\Packeges\Models\Validation;
use Illuminate\Contracts\Support\Arrayable;
use File, View, HTML;

/**
 * Class TplModel
 * @package App\Models
 */
abstract class TplModel implements TplInterface, Arrayable
{
    /**
     * @var string
     */
    protected $tplpath;
    /**
     * @var string
     */
    public $configs = [
        'hf' => 'hf.json',
        'page_sections' => 'page_sections.json',
        'sections' => 'sections.json',
        'units' => 'units.json',
        'main_body' => 'main_body.json',
        'templates' => 'templates.json',
        'assets' => 'framework_assets.json'
    ];

    public static $type;


    /**
     * @var
     */

    /**
     * @var
     */
    private $path;
    /**
     * @var
     */
    public $appPath;
    public $folders=[];
    /**
     * @var
     */
    protected $attributes;
    /**
     * @var
     */
    protected $original;
    /**
     * @var null
     */
    protected static $_instance = null;

    /**
     * @var
     */
    public $dir;

    /**
     * @var array
     */
    public $before = [];


    /**
     * TplModel constructor.
     */
    public function __construct()
    {
        if (!static::path()) return null;
    }

    /**
     * @param null $path
     * @return null
     */
    public static function get($path = null)
    {

        if (self::$_instance === null) {
            self::$_instance = (new static);
        }

        static::$_instance->attributes = json_decode(File::get($path), true); // TODO: Change the autogenerated stub
        static::$_instance->original = json_decode(File::get($path), true); // TODO: Change the autogenerated stub
        return static::$_instance;
    }

    /**
     * @return mixed
     */
    public  function getAll($selfType=null)
    {
            $configFileData = $this->getRegisteredDataFromFileBySelfType($selfType);
            if($configFileData) {
                foreach($configFileData as $key => $currentGear) {
                    if($this->checkGearInFileStructureWithPath($currentGear['path']) && strtolower($selfType) == strtolower($currentGear['self_type'])) {
                        $tpl=new static;
                        $tpl->original = $tpl->attributes = $currentGear;
                        $tpl->path = base_path($currentGear['path']);
                        $tpl->folders[] = $tpl->main_type . DS . $tpl->type . DS . $tpl->slug;
                        $tpl->folders[] = $tpl->type . DS . $tpl->slug;
                        $this->before[] = $tpl;
                    }
                }
            }
            return $this;
        }

    /**
     * @return mixed
     */
    public function toArray()
    {
        if (isset($this->attributes)) return $this->attributes;
        return false;
    }


    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function scopeWhere($key, $value)
    {
        $array = [];
        $all = $this->before;

        foreach ($all as $keyeer => $befores) {
            $conf = $befores->toArray();
            if (isset($conf[$key]) && $conf[$key] == $value) {
                $array[] = $befores;
            }
        }
        $this->before = collect($array);
        return $this;
    }

    /**
     * @param $key
     * @param $value
     * @return array
     */
    public function stWhere($key, $value)
    {

        if (!empty($this->before)) {
            return $this->scopeWhere($key, $value);
        }
        $array = array();
        foreach ($this->before as $static) {
            if (isset($static->toArray()[$key]) && $static->toArray()[$key] == $value)
                $array[] = $static;
        }
        $this->before = collect($array);
        return $this;
    }

    /**
     * @param $key
     * @param null $value
     * @return array
     */
    protected function listing($key, $value = null)
    {
        $lusts = array();
        if (!$this->before) {
            $tpl = $this->getAll();
            foreach ($tpl->before as $template) {
                $lusts[$template->toArray()[$key]] = $template->toArray()[$value];
            }
            return $lusts;
        }
        foreach ($this->before as $template) {
            $lusts[$template->toArray()[$key]] = $template->toArray()[$value];
        }
        return $lusts;
    }

    /**
     * @param array $array
     * @return TplModel
     */
    public function createVariation(array $array)
    {
        $variations = $this->variations;
        $variations[uniqid() . '.' . $this->slug] = $array;
        $this->setAttributes('variations', $variations);
        return $this->save();
    }

    /**
     * @param $id
     * @param array $array
     * @return TplModel|bool
     */
    public function updateVariation($id, array $array)
    {
        $variations = $this->variations;
        if (!isset($variations[$id])) return false;
        $variations[$id] = $array;
        $this->variations = $variations;
        return $this->save();
    }

    /**
     * @return mixed
     */
    public function getConfiguration()
    {
        $conf = $this->path . '/configuration.php';
        $array = [];
        if (File::exists($conf)) {
            $array = File::getRequire($conf);
        }
        return replaceConfigurationTemplatePath($array, $this->folders[0]);
    }

    /**
     * @return mixed
     */
    public function getCostumiser()
    {
        $conf = $this->path . '/customiser.php';
        $array = [];
        if (File::exists($conf)) {
            $array = File::getRequire($conf);
        }
        return $array;
    }

    /**
     *
     */
    public function getRequireLogic()
    {
        $conf = $this->path . '/logic.php';
        $array = [];
        if (File::exists($conf)) {
            require_once($conf);
        }
    }

    /**
     * @return mixed
     */
    public function recursiveFindAllWidgets()
    {
        $templates = $this->getAll()->run();
        $paths = [];
        foreach ($templates as $template) {
            $paths[] = $template->path . DS . 'installable/widgets';
        }
        return $this->makeWidgetTemplate($paths);

    }

    public function recursiveFindAllUnits()
    {
        $templates = $this->getAll()->run();
        $paths = [];
        foreach ($templates as $template) {
            $paths[] = $template->path . DS . 'installable/units';
        }
        return $this->makeWidgetTemplate($paths);

    }




    /**
     * @param $paths
     * @return mixed
     */
    protected function makeWidgetTemplate($paths)
    {
        $array = [];
        foreach ($paths as $path) {
            if (File::isDirectory($path)) {
                $subTypes = File::directories($path);
                foreach ($subTypes as $type) {//sub type
                    if (file::exists($type . DS . $this->config)) {
                        $tpl = new $this;
                        $conf = $type . DS . $tpl->config;
                        $tpl->attributes = json_decode(File::get($conf), true); // TODO: Change the autogenerated stub
                        $tpl->original = json_decode(File::get($conf), true); // TODO: Change the autogenerated stub
                        $tpl->path = $type;
                        $array[] = $tpl;
                    }
                }
            }
        }
        return collect($array);
    }

    /**
     * @param array $variables
     * @return string
     */
    public function render(array $variables = [])
    {
        $slug = $this->slug;
        View::addLocation(realpath($this->path));
        View::addNamespace("$slug", realpath($this->path));
        $path=$this->path;
        if ($this->autoinclude) return $this->getAutoInclude()->getRender($variables['variation']->toArray(), "$slug::");
        if($this->main_file){
            $tpl = str_replace(".blade.php","",$this->main_file);
            if(isset($variables['view_name'])){
                $tpl = $variables['view_name'];
            }
        }else{
            $tpl = "tpl";
        }

        return View::make("$slug::$tpl")->with($variables)->with(['tplPath' => $path,'_this'=>$this])->render();
    }

    /**
     * @param array $variables
     * @return string
     */
    public function renderSettings(array $variables = [])
    {
        $path=$this->path;
        $variables['tplPath']=$path;
        $variables['_this']=$this;
        $slug = $this->slug;
        if(!File::exists($path.'/settings.blade.php'))return "Undefined Settings Blade!";
        View::addLocation(realpath($this->path));
        View::addNamespace("$slug", realpath($this->path));
        return View::make("$slug::settings")->with($variables)->render();
    }
    public function renderStyles(array $variables = [])
    {
        $path=$this->path;
        $variables['tplPath']=$path;
        $variables['_this']=$this;
        $slug = $this->slug;
        if(!File::exists($path.'/styles.blade.php'))return 'No Styles providet from this widget';
        View::addLocation(realpath($this->path));
        View::addNamespace("$slug", realpath($this->path));
        return View::make("$slug::styles")->with($variables)->render();
    }
    public function renderOptions(array $variables = [])
    {
        $path=$this->path;
        $variables['tplPath']=$path;
        $variables['_this']=$this;
        $slug = $this->slug;
        if(!File::exists($path.'/options.blade.php'))return 'No Options providet from this widget';
        View::addLocation(realpath($this->path));
        View::addNamespace("$slug", realpath($this->path));
        return View::make("$slug::options")->with($variables)->render();
    }

    /**
     * @param $slug
     * @return null
     */
    public static function find($slug)
    {
        $tpl = null;
        $getLatesClassObj = get_called_class();
        $instance = new static;
        $instance->getAll($getLatesClassObj::$type);
        foreach ($instance->before as $static) {
            $attrs = $static->toArray();
            if (isset($attrs['slug']) && $attrs['slug'] == $slug) {
                $tpl = $static;
            }
        }
        $instance->before = $tpl;
        return $instance->run();
    }
    private static function parseGearsConfigFileToArray() {
        $configFilePath = self::getGearsConfigFilePath();
        if(File::isFile($configFilePath)) {
            $configs = File::get($configFilePath);
            if($configs) {
                return json_decode($configs, true);
            }
            return false;
        }
    }

    private static function getRegisteredDataFromFiles() {
        $result = [];
        foreach(self::$configs as $config) {
            $configFilePath = storage_path('app' . DS . $config);
            if(File::isFile($configFilePath)) {
                $configs = File::get($configFilePath);
                if($configs) {
                    $result = array_merge($result, json_decode($configs, true));
                }
            }
        }
        return $result;
    }

    public function getRegisteredDataFromFileBySelfType($selfType) {
        if($selfType) {
            $configFileName = isset($this->configs[strtolower($selfType)]) ? $this->configs[strtolower($selfType)] : null;
            $configFilePath = storage_path('app' . DS . $configFileName);
            if(File::isFile($configFilePath)) {
                $configs = File::get($configFilePath);
                if($configs) {
                    return json_decode($configs, true);
                }
            }
        }
        return false;
    }

    private static function getGearsConfigFilePath() {
        return config('paths.GEARS_CONFIG_FILE') . DS. config('config.GEARS_CONFIG_FILE_NAME');
    }

    public function checkGearInFileStructureWithPath($path) {
        if($path) {
            return File::isDirectory($path);
        }
    }
    /**
     * @return bool
     */
    public function delete()
    {
        return File::deleteDirectory($this->path);
    }

    /**
     *
     */

    /**
     * @return array
     */
    public function run()
    {
         return $this->before;
    }

    /**
     * @return mixed
     */
    public function first()
    {
        if (count($this->before)) return $this->before[0];
        return null;
    }

    /**
     * @param $name
     * @param $arguments
     */
    public function __call($name, $arguments)
    {
        if ($name === 'where') {
            return call_user_func_array([$this, 'stWhere'], $arguments);
        }
        if ($name === 'lists') {
            return call_user_func_array([$this, 'listing'], $arguments);
        }

    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        $result = isset($this->toArray()[$name]) ? $this->toArray()[$name] : false;
        return $result;
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
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return mixed
     */
    public static function getDir()
    {
        return self::$dir;
    }

    /**
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * @return string
     */
    public function getTplpath()
    {
        return $this->tplpath;
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param $attributes
     */
    public function setAttributes($key, $value)
    {
        $attributes = $this->attributes;
        $attributes[$key] = $value;
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @param $dir
     */
    public static function setDir($dir)
    {
        self::$dir = $dir;
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param $path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param $namspace
     * @param $primary
     * @return mixed
     */
    protected function belongsTo($namspace, $primary)
    {
        return $namspace::find($this->$primary);
    }

    /**
     * @param $namspace
     * @param string $primary
     * @param string $foreign
     * @return mixed
     */
    protected function hasMany($namspace, $primary = 'template_id', $foreign = 'slug')
    {
        return $namspace::where($primary, $this->$foreign)->get();
    }

    /**
     * @param $name
     * @param $value
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
     * @return $this
     */
    public function save()
    {
        $conf = $this->path . DS . $this->config;
        File::put($conf, json_encode($this->toArray()));
        return $this;
    }


    /**
     * @return mixed
     */
    public function path($path = null)
    {
        $path = ($path) ? base_path($this->tplpath . DS . $path) : base_path($this->tplpath);

        if (!File::isDirectory($path)) return false;
        $this->dir = $path;
        return $this;
    }

    /**
     * @return null
     */
    public static function instance()
    {
        if (!static::$_instance) {
            static::$_instance = new static;
        }
        return static::$_instance;
    }

    /**
     * @param $namspace
     * @return mixed
     */
    public function allVars($namspace)
    {
        $v = new $namspace();
        return $v->findV($this->path);
    }

    /**
     *
     */


    public static function __callStatic($name, $arguments)
    {
        $instance = new static;
        if ($name === 'where') {
            return call_user_func_array([$instance, 'stWhere'], $arguments);
        }
        if ($name === 'all') {
            return call_user_func_array([$instance, 'getAll'], $arguments);
        }
        if ($name === 'allWidgets') {
            return call_user_func_array([$instance, 'recursiveFindAllWidgets'], $arguments);
        }
        if ($name === 'lists') {
            return call_user_func_array([$instance, 'listing'], $arguments);
        }
    }

    /**
     *
     */
    public function __destruct()
    {
//        unset($this->before);


        // TODO: Implement __destruct() method.
    }
    public function style($path)
    {
        return \Html::style('units/styles/'.$this->self_type.'/'.$this->slug.'/'.$path);
    }

    public function script($path)
    {
        return \Html::script('units/scripts/'.$this->self_type.'/'.$this->slug.'/'.$path);
    }

    /**
     * @param $path
     * @return string
     */
    public function asset($path)
    {
        return HTML::image('appdata/template/image/avatar.png');
    }
    public function customiserFields(){
        $costumiser=$this->getCostumiser();
        dd($costumiser);
    }

    public function setTplPath($path) {
        $this->tplpath = $path;
    }

}