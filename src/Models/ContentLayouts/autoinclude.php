<?php
/**
 * Created by PhpStorm.
 * User: menq
 * Date: 6/8/17
 * Time: 9:46 PM
 */

namespace Btybug\btybug\Models\ContentLayouts;

use File;

/**
 * Trait autoinclude
 * @package Btybug\btybug\Models\ContentLayouts
 */
trait autoinclude
{
    /**
     * @return $this
     */
    public function makeAutoInclude()
    {
        if (File::exists($this->path . DS . 'auto_include.json') && File::isDirectory($this->path . DS . '_partials') && $this->autoinqlude) {
            return $this;
        }
        $this->setAttributes('autoinclude', true);
        $variation = $this->scopeMakeVariation();
        $data = [$variation->id => 'auto_generated'];
        $this->setAttributes('autoinclude_main', $variation->id);
        File::put($this->path . DS . 'auto_include.json', json_encode($data, true));
        if (!File::isDirectory($this->path . DS . '_partials')) {
            File::makeDirectory($this->path . DS . '_partials');
        }
        File::put($this->path . DS . '_partials' . DS . 'auto_generated.blade.php', '<code>Example Autoinclud File</code>');
        $variation->save();
        $this->save();
        return $this;
    }

    public function makeAutoIncludeVariation($name = null, $data = [], $content = null)
    {

        $auto_include_json = json_decode(File::get($this->path . DS . 'auto_include.json'), true);
        if (!$content) {
            $content = File::get($this->path . DS . '_partials' . DS . $auto_include_json[$this->autoinclude_main] . '.blade.php');
        }
        $variation = $this->scopeMakeVariation($data);
        $auto_include_json[$variation->id] = $name ? $name : uniqid('auto_include');
        File::put($this->path . DS . 'auto_include.json', json_encode($auto_include_json, true));
        File::put($this->path . DS . '_partials' . DS . $auto_include_json[$variation->id] . '.blade.php', $content);
        return $variation;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function getAutoInclude()
    {
        if (File::exists($this->path . DS . 'auto_include.json')) {
            $this->setAttributes('partials', json_decode(File::get($this->path . DS . 'auto_include.json'), true));
            return $this;
        }
        throw new \Exception('Missing auto_include.json file in ' . $this->path . '!!!', 404);
    }

    /**
     * @param $variation
     * @param $hint_path
     * @return string
     * @throws \Exception
     */
    protected function getRender($variation, $hint_path)
    {
        if (isset($variation['id']) && isset($this->partials[$variation['id']])) {
            if (\View::exists($hint_path . '._partials.' . $this->partials[$variation['id']])) {
                $inqlude = $hint_path . '._partials.' . $this->partials[$variation['id']];
                $html = \View::make($inqlude)->with(['settings' => $variation, '_this' => $this])->render();
                return $html;

            } else {
                throw new \Exception($variation['id'] . ' variation partial file doesn\'t exists!!!', 404);
            }
        } elseif ($this->autoinclude_main) {
            if (\View::exists($hint_path . '._partials.' . $this->partials[$this->autoinclude_main])) {
                $inqlude = $hint_path . '._partials.' . $this->partials[$this->autoinclude_main];
                $html = \View::make($inqlude)->with(['settings' => $variation, '_this' => $this])->render();
                return $html;
            }
        }
        throw new \Exception('Missing variation id!!!', 404);
    }
}