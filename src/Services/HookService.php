<?php
/**
 * Copyright (c) 2017. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace Btybug\btybug\Services;

use Btybug\btybug\Models\ContentLayouts\ContentLayouts;
use Btybug\btybug\Repositories\HookRepository;
use Btybug\btybug\Services\GeneralService;

/**
 * Class HookService
 * @package Btybug\Console\Services
 */
class HookService extends GeneralService
{
    private $hookRepo;

    public function __construct(HookRepository $hookRepository)
    {
        $this->hookRepo = $hookRepository;
    }

    public function save(array $data)
    {
        $authorID = (isset($data['author_id'])) ? $data['author_id'] : \Auth::id();
        $type = (isset($data['type'])) ? $data['type'] : "layout";
        $help_text = (isset($data['help_text'])) ? $data['help_text'] : null;

        return $this->hookRepo->create([
           'name' => $data['name'],
           'tag'  => $data['tag'],
           'slug' => md5(uniqid()),
            'type' => $type,
            'author_id' => $authorID,
            'help_text' => $help_text
        ]);
    }
}