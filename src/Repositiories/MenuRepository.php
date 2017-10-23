<?php

namespace Sahakavatar\Cms\Repositories;

use Sahakavatar\Cms\Models\Menu;


class MenuRepository extends GeneralRepository
{

    public function model()
    {
        return new Menu();
    }

    public function getWhereNotPlugins()
    {
        return $this->model->where('type', '!=', 'plugin')->get();
    }

    public function getWhereNotPluginsFirst()
    {
        return $this->model->where('type', '!=', 'plugin')->first();
    }
}