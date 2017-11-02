<?php

/**
 * Created by PhpStorm.
 * User: Ara Arakelyan
 * Date: 7/19/2017
 * Time: 3:08 PM
 */

namespace Btybug\btybug\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function response(array $errors)
    {
        return parent::response($errors);
    }
}
