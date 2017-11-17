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

class Menu extends Model
{
    protected $table = 'demo_menus';

    protected $guarded = array('id');

    public static function registerFromAdminPages($pages, $type = 'plugin', $parent = null)
    {
        if (count($pages)) {
            foreach ($pages as $page) {
                $parentMenu = self::create([
                    'admin_page_id' => $page->id,
                    'module' => $page->module_id,
                    'name' => $page->title,
                    'url' => $page->url,
                    'type' => $type,
                    'parent_id' => $parent,
                ]);

                if (count($page->childs)) {
                    self::registerFromAdminPages($page->childs, $type, $parentMenu->id);
                }
            }
        }

        return false;
    }

    public static function saveFromJson($items, $menu, $role, $parent = 0)
    {
        if (count($items)) {
            foreach ($items as $item) {
                $id = uniqid();
                $result = MenuItems::create([
                    'id' => $id,
                    'menu_id' => $menu->id,
                    'parent_id' => $parent,
                    'page_id' => $item['id'],
                    'role_id' => $role->id,
                    'title' => $item['title'],
                    'url' => $item['url'],
                ]);

                if ($result && isset($item['children'])) {
                    self::saveFromJson($item['children'], $menu, $role, $id);
                }
            }
        }
    }

    public function adminPage()
    {
        return $this->belongsTo('Btybug\Console\Models\AdminPages');
    }

    public function creator()
    {
        return $this->belongsTo('Btybug\User\User', 'creator_id', 'id');
    }

    public function items()
    {
        return $this->hasMany('Btybug\btybug\Models\MenuItems', 'menu_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('Btybug\btybug\Models\MenuItems', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany('Btybug\btybug\Models\MenuItems', 'parent_id');
    }
}