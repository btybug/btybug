<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 7/9/2016
 * Time: 8:12 PM
 */

namespace Btybug\btybug\Models\Templates\Eloquent\Abstractions;

use File;

/**
 * Class TplVariations
 * @package Btybug\btybug\Models\Eloquent\Abstractions
 */
abstract class TplVariations
{
    protected static $_instanceV = null;
    /**
     * @var
     */
    public $updated_at;
    /**
     * @var
     */
    protected $variationPath;
    /**
     * @var null
     */
    protected $template;
    /**
     * @var string
     */
    protected $path;
    protected $file;
    /**
     * @var
     */
    protected $id;
    /**
     * @var
     */
    protected $attributes;
    /**
     * @var
     */
    protected $original;

    /**
     * TplVariations constructor.
     */
    public function __construct($tpl = null)
    {
        if ($tpl) {
            $this->template = $tpl;
            $this->variationPath = $tpl->variationPath;
            $this->path = $tpl->getPath() . '/' . $this->variationPath;
        }
    }

    /**
     * @return null
     */
    public static function instanceV()
    {
        if (!static::$_instanceV) {
            static::$_instanceV = new static;
        }
        return static::$_instanceV;
    }

    /**
     * @param $id
     * @param $tpl
     * @return bool
     */
    public static function delete($id, $tpl)
    {
        $vPath = $tpl->path . DS . $tpl->variationPath . DS . $id . '.json';
        if (File::exists($vPath)) return File::delete($vPath);
        return false;
    }

    /**
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        $instance = new static;

        return call_user_func_array([$instance, $method], $parameters);
    }

    /**
     * @param array $arg
     * @return mixed
     */
    abstract function renderVariation(array $arg = []);
//    public static function __callStatic($name, $arguments)
//    {
//        dd($name);
//        if($name=='findVariation'){
//            call_user_func_array([static::instanceV(),'scopeFindVarition'],$arguments);
//        }
//        // TODO: Implement __callStatic() method.
//    }

    /**
     * @param $tpl
     * @param $id
     * @return mixed
     */
    abstract public function findVarition($tpl, $id);

    /**
     * @return $this
     */
    public function findV($tpl)
    {
        if(is_dir($tpl.'/variations')){
            $path =$tpl . '/variations';
        }else{
            $path =base_path($tpl) . '/variations';
        }
        $vars = File::allFiles($path);
        $array = array();
        foreach ($vars as $var) {
//            dd($var->getRelativePathname());
            if (File::extension($var) == 'json') {
                $all = new $this;
                $all->id = File::name($var);
                $all->path = $path . '/' . File::name($var) . '.json';
                $all->file = $var;
                $all->attributes = json_decode(File::get($var), true);
                $all->original = $all->attributes;
                $all->updated_at = File::lastModified($var);
                $array[] = $all;
            }
        }
        return collect($array);
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
     * @param $key
     * @param $value
     * @return $this
     */
    public function setAttributes($key, $value)
    {
        $attributes = $this->attributes;
        $attributes[$key] = $value;
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * @return $this
     */
    public function save()
    {
        $conf = empty($this->file) ? $this->path : $this->file;
        File::put($conf, json_encode($this->toArray(), true));
        return $this;
    }

    /**
     *
     */
    public function scopeDelete()
    {

    }

    /**
     * @param $tpl
     * @param $array
     * @return mixed
     */
    abstract public function createVariation($tpl, $array);

    /**
     * @param $namspace
     * @param $primary
     * @return mixed
     */
    protected function belongsTo($namspace, $primary)
    {
        return $namspace::find($this->$primary);
    }

}