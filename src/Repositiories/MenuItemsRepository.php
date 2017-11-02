<?php

namespace Btybug\btybug\Repositories;

use Btybug\btybug\Models\MenuItems;


class MenuItemsRepository extends GeneralRepository
{

    public function model()
    {
        return new MenuItems();
    }
}