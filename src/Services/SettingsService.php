<?php

namespace Btybug\btybug\Services;


use Btybug\btybug\Repositories\AdminsettingRepository;
use Btybug\Console\Repository\AdminPagesRepository;
use Btybug\Console\Repository\FrontPagesRepository;
use Btybug\User\Repository\RoleRepository;

class SettingsService extends GeneralService
{

    public $adminPageRepo;
    public $adminsettingRepository;

    public function __construct (
        AdminsettingRepository $adminsettingRepository,
        AdminPagesRepository $adminPageRepo
    )
    {
        $this->adminsettingRepository = $adminsettingRepository;
        $this->adminPageRepo = $adminPageRepo;
    }

    public function changeSitePageSettings (array $data)
    {
        $this->adminPageRepo->updateAll([
            'footer'    => $data['footer'],
            'layout_id' => $data['backend_page_section'],
            'settings'  => isset($data['placeholders']) ? json_encode(['placeholders' => $data['placeholders']], true) : null
        ]);
        $this->adminsettingRepository->createOrUpdateToJson($data, 'backend_site_settings', 'backend_site_settings');
    }
}