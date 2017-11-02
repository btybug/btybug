<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 7/9/2016
 * Time: 1:05 AM
 */

namespace Btybug\btybug\Models\Templates\Eloquent\Interfaces;


interface TplInterface
{

    /**
     * @param null $path
     * @return mixed
     */
    public static function get($path = null);

    /**
     * @param $slug
     * @return mixed
     */
    public static function find($slug);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function stWhere($key, $value);

    /**
     * @return mixed
     */
    public function getAll();

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function scopeWhere($key, $value);

    /**
     * @param array $array
     * @return mixed
     */
    public function createVariation(array $array);

    /**
     * @return mixed
     */
    public function run();

    /**
     * @return mixed
     */
    public function first();

    /**
     * @return mixed
     */
    public function getTplpath();

    /**
     * @return mixed
     */
    public function getConfig();

    /**
     * @return mixed
     */
    public function getConfiguration();

    /**
     * @return mixed
     */
    public function getCostumiser();

    /**
     * @return mixed
     */
    public function render();
}

