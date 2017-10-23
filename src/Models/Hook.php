<?php

namespace Sahakavatar\Cms\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Class Hook
 * @package Sahakavatar\Cms\Models
 */
class Hook extends Model
{
    /**
     * @var string
     */
    protected $table = 'hooks';

    /**
     * @var array
     */
    protected $guarded = array('id');

    protected $casts = [
        'data' => 'json'
    ];
}