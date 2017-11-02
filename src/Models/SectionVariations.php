<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 7/10/2016
 * Time: 5:24 PM
 */

namespace Btybug\btybug\Models;

use File;
use Btybug\btybug\Models\Templates\Eloquent\Abstractions\TplVariations as variations;

class SectionVariations extends variations
{
    /**
     * @param array $arg
     * @return mixed
     */
    public function renderVariation(array $arg = [])
    {
        $slug = explode('.', $this->id);
        return Sections::find($slug[0])->render(['variation' => $this->settings, 'args' => $arg]);
    }

    public function findVarition($tpl, $id)
    {
        $path = $tpl->path . '/' . $tpl->variationPath . '/' . $id . '.json';
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
        $path = $tpl->path . '/' . $tpl->variationPath . '/' . $id . '.json';
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