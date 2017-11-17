<?php
namespace Btybug\btybug\Repositories;


use Btybug\btybug\Models\Settings;
use Btybug\btybug\Repositories\GeneralRepository;

class AdminsettingRepository extends GeneralRepository
{


    public function defaultSettings()
    {
        $media_settings = [];
        $rs = $this->findAllBy('section', 'media');
        foreach ($rs as $sngl_setting) {
            $media_settings[$sngl_setting->settingkey] = $sngl_setting->val;
        }
        return $media_settings;
    }

    public function updateMedia($settings)
    {
        foreach ($settings as $key => $val) {
            $media = $this->model->where('settingkey', $key)->first();

            if (!$media) {
                $this->create(['settingkey' => $key, 'section' => 'media', 'val' => $val]);
            } else {
                $this->update(['val' => $val], $media->id);
            }
        }
    }

    public function getAllowedExt($section)
    {
        if ($section == 'media') {
            $extensions = '';
            $rs = $this->model->whereIn('settingkey', ['allowed_img_ext', 'allowed_vid_ext', 'allowed_doc_ext'])->get();
            foreach ($rs as $rec) {
                $extensions .= ',' . $rec->val;
            }

            return substr($extensions, 1);
        }
    }

    public function getAllowedSize($extension)
    {
        $rs = $this->model->where('settingkey', 'allowed_img_ext')->first();
        $allowed_ext = $rs->val;
        $allowed_ext_arr = explode(',', $allowed_ext);
        if (in_array($extension, $allowed_ext_arr)) {
            $rs = $this->model->where('settingkey', 'img_max_size')->first();

            return $rs->val;
        } else {
            return 100;
        }


    }

    /**
     * @param $data
     * @param string $section
     */
    public function updateSystemSettings($data, $section = "setting_system")
    {
        foreach ($data as $key => $val) {
            if (is_array($val)) $val = json_encode($val, true);
            $system = $this->model->where('settingkey', $key)->where('section', $section)->first();
            if (!$system) {

                $this->create(['settingkey' => $key, 'section' => $section, 'val' => $val]);
            } else {
                $this->update($system->id, ['val' => $val]);
            }
            $this->checkRegistration($key, $val);
        }
    }

    private function checkRegistration($key, $value)
    {
        if ($key == 'enable_registration' && $value == 0) {
            Forms::where('slug', '58vg8d7vw4nhn1')
                ->update(['blocked' => 1]);
        }
    }

    public function saveSocialApiKey($data)
    {
        $system = $this->model->where('settingkey', $data['name'])->first();
        if (!$system) {
            $result = $this->create(['settingkey' => $data['name'], 'section' => 'social', 'val' => $data['key']]);
        } else {
            $result = $this->update(['val' => $data['key']], $system->id);
        }

        return $result;
    }


    public function getSystemTimeOut()
    {
        $lr = $this->model->where('settingkey', 'login_timeout')->first();
        if ($lr) {
            return $lr->val;
        }

        return \Config::get('session.lifetime');
    }

    public function getSystemLoginReg($settingkey)
    {
        $lr = $this->model->where('settingkey', $settingkey)->first();

        if ($lr) {
            return ($lr->val) ? true : false;
        }

        return false;
    }

    public function getSystemSettings()
    {
        $system = $this->model->where('section', 'setting_system')->pluck('val', 'settingkey');

        return $system;
    }

    public function getSettings($section, $settingkey)
    {
        $system = $this->model->where('section', $section)->where('settingkey', $settingkey)->first();

        return $system;
    }

    public function getSettingsBySection($section)
    {
        $system = $this->model->where('section', $section)->first();

        return $system;
    }

    public function urlManager($data)
    {
        $system = $this->model->where('section', 'url_manager')->first();
        if (!$system) {
            if ($data['url'] == 'custom') {
                $result = $this->create(
                    ['settingkey' => $data['url'], 'section' => 'url_manager', 'val' => $data['custom']]
                );
            } else {
                $result = $this->create(['settingkey' => $data['url'], 'section' => 'url_manager']);
            }

        } else {
            $result = $this->update(['settingkey' => $data['url']], $system->id);
        }

        return $result;
    }


    /**
     * Provide a list of all Thimbs settings
     *
     * @return array|mixed
     */
    public function getThumbs()
    {
        $result = [];
        $rs = $this->model->select('val')->where('section', 'media')->where('settingkey', 'thumbs')->first();
        if ($rs && $rs->val != '') {
            $result = unserialize(@$rs->val);
        }

        return $result;
    }

    /**
     * Save new thumb info in already existing data for thimbnails
     *
     * @param $req as array of thumbs info
     * @return nothign
     */
    public function updateThumbs($req)
    {
        $data = '';
        $rs = $this->model->select('id', 'val')->where('section', 'media')->where('settingkey', 'thumbs')->first();
        if ($rs && $rs->val != '') {
            $data = unserialize(@$rs->val);
        }
        $data[] = $req;
        $data = serialize($data);
        $this->updateRich(['val' => $data], $rs->id);
    }

    /**
     * update Thumbs in bulk in one place
     *
     * @param $req as array
     */
    public function updateThumbBulk($req)
    {
        $data = '';
        $titles = $req['thumb']['title'];
        $widths = $req['thumb']['width'];
        $heights = $req['thumb']['height'];

        foreach ($titles as $key => $val) {
            if (trim($val) != '') {
                $data[] = ['title' => $val, 'width' => $widths[$key], 'height' => $heights[$key]];
            }
        }
        $rs = $this->model->select('id', 'val')->where('section', 'media')->where('settingkey', 'thumbs')->first();
        $data = serialize($data);
        $this->updateRich(['val' => $data], $rs->id);
    }

    public function getBackendSettings()
    {
        $data = $this->findBy('section', 'backend_settings');

        if ($data && $data->val) {
            return json_decode($data->val, true);
        }

        return null;
    }

    public function createOrUpdateToJson($data, $section, $settingkey)
    {
        $result = $this->findBy('settingkey', $settingkey);
        if ($result) {
            return $this->update($result->id, ['val' => json_encode($data, true)]);
        }
            return $this->create(['section' => $section, 'settingkey' => $settingkey,
                'val' => json_encode($data, true)]);
    }

    public function createOrUpdate($data, $section, $settingkey)
    {
        $result = $this->findBy('settingkey', $settingkey);
        if ($result) {
            $this->update($result->id, $data);
        } else {
            $this->create(['section' => $section, 'settingkey' => $settingkey,
                'val' => $data]);
        }
    }

    public function getVersionsSettings($section, $key)
    {
        $result = $this->model()->where('section', $section)->where('settingkey', $key)->first();
        if ($result) {
            return json_decode($result->val, true);
        }

        return [];
    }

    protected function model()
    {
        return new Settings();
    }
}
