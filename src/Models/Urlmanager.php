<?php

namespace Btybug\btybug\Models;

use Illuminate\Database\Eloquent\Model;

class Urlmanager extends Model
{

    protected $table = 'urlmanager';

    protected $dates = array('created_at', 'updated_at');

    protected $guarded = ['id'];

    public function childs()
    {
        $model = self::where('parent_id', $this->id)->get();
        return $model;
    }

    public function frontPage()
    {
        return $this->hasOne('App\Modules\Manage\Models\FrontendPage', 'id', 'front_page_id');
    }
}
