<?php
/**
 * Copyright (c) 2017. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

/**
 * Created by PhpStorm.
 * User: Comp2
 * Date: 2/8/2017
 * Time: 1:24 PM
 */

namespace Btybug\btybug\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItems extends Model
{
    public $incrementing = false;
    protected $table = 'demo_menu_items';
    protected $primaryKey = 'id';
    protected $fillable = array('id', 'menu_id', 'role_id', 'parent_id', 'page_id', 'title', 'url', 'icon', 'sort', 'created_at', 'updated_at');

    public static function registerFromAdminPages($pages, $type = 'plugin', $parent = null)
    {
        if (count($pages)) {
            foreach ($pages as $page) {
                $parnetMenu = self::create([
                    'admin_page_id' => $page->id,
                    'module' => $page->module_id,
                    'name' => $page->title,
                    'url' => $page->url,
                    'type' => $type,
                    'parent_id' => $parent,
                ]);

                if (count($page->childs)) {
                    self::registerFromAdminPages($page->childs, $type, $parnetMenu->id);
                }
            }
        }

        return false;
    }

    public function menu()
    {
        return $this->belongsTo('Btybug\btybug\Models\Menu', 'menu_id', 'id');
    }

    public function childs()
    {
        return $this->hasMany('Btybug\btybug\Models\MenuItems', 'parent_id', 'id');
    }
}