<?php
/**
 * Created by PhpStorm.
 * User: Comp2
 * Date: 11/17/2016
 * Time: 2:33 PM
 */

namespace Btybug\btybug\Models\Templates;

use File;
use Btybug\btybug\Models\Templates\Eloquent\Abstractions\TplVariations as variations;

class UnitsVariations extends variations
{
    /**
     * @var string
     */
    protected $templatesPath = 'units';

    /**
     * @var string
     */
    protected $variationPath = 'variations';

    /**
     * @param array $arg
     * @return mixed
     */
    public function renderVariation(array $arg = [])
    {
        $slug = explode('.', $this->id);
        return Units::find($slug[0])->render(['variation' => $this->settings, 'args' => $arg]);
    }

    public function findVarition($tpl, $id)
    {
        $path = $tpl->path . DS . $tpl->variationPath . DS . $id . '.json';
        if (File::exists($path)) {
            $all = new $this;
            $all->id = File::name($path);
            $all->path = $path;
            $all->file = '';
            $all->attributes = json_decode(File::get($path), true);
            $all->original = $all->attributes;
            $all->updated_at = File::lastModified($path);
            return $all;
        }
        return null;
    }

    public function createVariation($tpl, $array)
    {
        $id = $tpl->slug . '.' . uniqid();
        $path = $tpl->path . DS . $tpl->variationPath . DS . $id . '.json';
        $array['id'] = $id;
        $all = new $this;
        $all->id = $id;
        $all->path = $path;
        $all->attributes = $array;
        $all->original = $all->attributes;
        $all->updated_at = time();
        $array[] = $all;
        return $all;

    }

}