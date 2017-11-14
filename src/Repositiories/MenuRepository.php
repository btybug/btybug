<?php

namespace Btybug\btybug\Repositories;

use Btybug\btybug\Models\Menu;


class MenuRepository extends GeneralRepository
{

    public function model()
    {
        return new Menu();
    }

    public function getFrontend()
    {
        return $this->model->where('place','frontend')->get();
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