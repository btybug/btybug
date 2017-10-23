<?php

namespace Sahakavatar\Cms\Repositories;

use Sahakavatar\Cms\Models\MenuItems;


class MenuItemsRepository extends GeneralRepository
{

    public function model()
    {
        return new MenuItems();
    }
}