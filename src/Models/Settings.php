<?php

namespace Btybug\btybug\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'settings';

    protected $settinds;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['section', 'settingkey', 'val'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    public function newSettings(array $data)
    {

    }

    public function scopeMedia($query)
    {
        return $query->where('section', 'media');
    }

}


