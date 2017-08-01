<?php
/**
 * Created by PhpStorm.
 * User: Sahak
 * Date: 8/1/2016
 * Time: 9:34 PM
 */

namespace Sahakavatar\Cms\Models;

use File;
use View;
use App\Models\Corepage;
use App\Models\Templates\Templates;


/**
 * Class Layouts
 * @package App\Models
 */
class Layouts
{

    /**
     * @var string
     */
    protected $dir;
    /**
     * @var
     */
    protected $path;
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var
     */
    private $query;

    /**
     * Layouts constructor.
     */
    public function __construct()
    {
        $this->dir = base_path("resources/templates");
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    private function scopeAll()
    {
        $layouts = File::allFiles($this->dir);
        $all = [];
        foreach ($layouts as $layout) {
            if (File::extension($layout) == "json") {
                $content = @json_decode(File::get($layout), true);
                dd($content);
                if ($content) {
                    $obj = new $this;
                    $obj->path = $this->dir . "/" . $content["id"] . ".json";
                    $obj->attributes = $content;
                    $all[] = $obj;
                }
            }
        }
        return collect($all);
    }

    /**
     * @param $id
     * @return $this|null
     */
    private function scopeFind($id)
    {
        $this->path = $this->dir . '/' . $id . ".json";
        if (File::exists($this->path)) {
            $content = @json_decode(File::get($this->path), true);
            if ($content) {
                $this->attributes = $content;
            }
            return $this;
        }
        return null;

    }

    /**
     * @param $key
     * @param null $value
     * @return array
     */
    private function scopeLists($key, $value = null)
    {
        $lists = array();
        $layouts = $this->all();
        foreach ($layouts as $layout) {
            $lists[$layout->toArray()[$value]] = $layout->toArray()[$key];
        }

        return $lists;
    }

    /**
     * @param $json
     * @return bool
     */
    public static function create($json)
    {
        $data = @json_decode($json, true);
        if ($data) {
            $_this = new static();
            $id = uniqid();
            $data['id'] = $id;
            $bytes_written = File::put($_this->dir . '/' . $id . '.json', json_encode($data));
            return $_this->scopeFind($id);
        }
        return false;
    }

    /**
     * @param $name
     * @return bool|mixed
     */
    public function __get($name)
    {
        $result = isset($this->toArray()[$name]) ? $this->toArray()[$name] : false;
        return $result;
    }

    /**
     * @return array|bool
     */
    public function toArray()
    {
        if (isset($this->attributes)) return $this->attributes;
        return false;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @return string
     */
    public function getJson()
    {
        return json_encode($this->attributes, true);
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
     * @return bool
     */
    public function delete()
    {
        return File::delete($this->path);
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
     * @return $this
     */
    public function save()
    {
        $conf = $this->path;
        File::put($conf, json_encode($this->toArray()));
        return $this;
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

    public function scopeUpdate()
    {

    }

    public function scopeRender($id)
    {
        $page = Corepage::find($id);
        $layout = $this->scopeFind($page->layout_id);
        $json_data = json_decode($page->data_option, true);
        $tplHtml = '<p>Undefined Template</p>';
        if (isset($json_data['main_body'])) {
            $tpl = Templates::find($json_data['main_body']);
            if ($tpl) {
                $tplHtml = $tpl->render();
            }

        }

        $html = View::make('frontend.page', compact(['layout', 'page', 'tplHtml']))->render();
        return $html;
    }

}