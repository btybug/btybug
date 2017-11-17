<?php
/**
 * Created by PhpStorm.
 * User: Edo
 * Date: 2/13/2017
 * Time: 3:07 PM
 */

namespace Btybug\btybug\Services;

use File;
use Illuminate\Http\Request;
use Btybug\btybug\Helpers\helpers;
use Btybug\btybug\Models\ContentLayouts\ContentLayouts;
use Btybug\btybug\Models\Templates\TplVariations;
use Btybug\btybug\Models\Templates\Units;
use Btybug\btybug\Models\Templates\UnitsVariations;
use Btybug\Modules\Models\Fields;
use Zipper;

/**
 * Class CmsItemUploader
 * @package App\Core
 */
class CmsItemUploader
{
    /**
     *
     */
    const ZIP = ".zip";

    /**
     *
     */
    const MIN_SIZE = 3;

    /**
     * @var mixed
     */
    public $uf;

    /**
     * @var
     */
    public $fileNmae;

    /**
     * @var
     */
    public $helper;

    /**
     * @var
     */
    public $generatedName;

    /**
     * @var
     */
    public $generatedSlug;
    public $rule = ['main_body' => [
        'self_type' => "require|in:main_body",
        'title' => "require|in:main_body",
        'type' => "require|alpha_dash",

    ]];
    /**
     * @var
     */
    protected $dir;
    /**
     * @var
     */
    private $folder;
    /**
     * @var
     */
    private $name;
    /**
     * @var
     */
    private $slug;
    /**
     * @var
     */
    private $place;
    /**
     * @var bool
     */
    private $gear;
    /**
     * @var
     */
    private $path;
    /**
     * @var
     */
    private $model;
    /**
     * @var
     */
    private $self_type;

    /**
     * UnitUpload constructor.
     */
    public function __construct($gear)
    {
        $this->helpers = new helpers;
        $this->uf = config('paths.ui_elements_uplaod');
        $this->gear = $this->getGearPathByType($gear);
        $this->model = $this->gear['model'];
        $this->path = $this->gear['path'];
        $this->self_type = $this->gear['self_type'];
    }

    /**
     * @param $gear
     * @return bool
     */
    private function getGearPathByType($gear)
    {
        $gearsArray = [
            'units' => [
                'path' => config('paths.units_uplaod'),
                'model' => "Btybug\\Cms\\Models\\Templates\\Units",
                'variation' => "Btybug\\Cms\\Models\\Templates\\UnitsVariations",
                'self_type' => 'units',
                'required_keys' => [
                    'title' => true,
                    'type' => true,
                    'tags' => true,
                ], 'equal_keys' => [
                    'type' => 'unit'
                ]
            ],
            'page_sections' => [
                'path' => 'resources' . DS . 'views' . DS . 'ContentLayouts' . DS,
                'model' => "Btybug\\Cms\\Models\\ContentLayouts\\ContentLayouts",
                'variation' => "Btybug\\Cms\\Models\\ContentLayouts\\ContentLayoutVariations",
                'self_type' => 'page_sections',
                'required_keys' => [
                    'self_type' => true,
                    'title' => true,
                    'type' => true
                ],
                'equal_keys' => [
                    'self_type' => 'page_sections'
                ]
            ]
        ];

        return isset($gearsArray[$gear]) ? $gearsArray[$gear] : false;
    }

    /**
     * @param $data
     * @param $code
     * @param null $links
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function ResponseSuccess($data, $code, $links = null, $id = null)
    {
        return \Response::json(['data' => $data, 'invalid' => false, 'id' => $id, 'links' => $links, 'code' => $code, 'error' => false], $code);
    }

    /**
     * @param $data
     * @param $code
     * @param $messages
     * @return \Illuminate\Http\JsonResponse
     */
    public function ResponseInvalid($data, $code, $messages)
    {
        return \Response::json(['data' => $data, 'invalid' => true, 'messages' => $messages, 'code' => $code, 'error' => false], $code);
    }

    /**
     * @param $request
     * @param string $place
     * @return array|\Illuminate\Http\JsonResponse|string
     */
    public function run($request, $place = 'backend')
    {
        $isValid = $this->isCompress($request);
        if (!$isValid) return $this->ResponseError('Uploaded data is InValid!!!', 500);
        $response = $this->upload($request);
        $response['place'] = $place;

        if (!$response['error']) {
            $result = $this->validatConfAndMoveToMain($response);
            if (!$result['error']) {
                File::deleteDirectory($this->uf, true);
                $basePath = base_path($this->path . $result['data']['folder']);
                $registerPath = $this->path . $result['data']['folder'];
                if(CmsItemRegister::registerGear($basePath, $registerPath, $response['place'], $this->self_type)){
                    $variation = $this->makeVariations($result['data']);
                    $this->registerField($basePath, $variation);
                    return $result;
                }else{
                    $response['error']=true;
                    $response['code']=301;
                    $response['message']='unit exists';
                };
            }
        }
            File::deleteDirectory($this->uf, true);
            return $response;
    }

    //uxel ver@ nshvac cod@

    /**
     * @param $file
     * @return bool|int|string
     */
    private function isCompress($request)
    {
        $file = $request->file('file');
        $ext = $file->getClientOriginalExtension();
        if ($file->getSize() < self::MIN_SIZE) {
            return false;
        }
        if ($ext == 'zip') {
            return 'zip';
        }
        return 0;
    }

    /**
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function ResponseError($message, $code)
    {
        return \Response::json(['message' => $message, 'code' => $code, 'error' => true], $code);
    }

    /**
     * @param Request $request
     * @return array
     */

    public function upload(Request $request)
    {

        if ($request->hasFile('file')) {

            $extension = $request->file('file')->getClientOriginalExtension(); // getting image extension
            $zip_file = $request->file('file')->getClientOriginalName();
            $this->fileNmae = str_replace(self::ZIP, "", $zip_file);
            $request->file('file')->move($this->uf, $zip_file); // uploading file to given path

            try {
                $this->extract();
            } catch (Exception $e) {
                return ['message' => $e->getMessage(), 'code' => $e->getCode(), 'error' => true];
            }

            return ['folder' => $this->generatedName, 'slug' => $this->generatedSlug, 'data' => $this->fileNmae, 'code' => 200, 'error' => false];
        }
    }

    /**
     *
     */
    public function extract()
    {
        $fileName = $this->fileNmae;
        $this->generatedSlug = uniqid();
        $this->generatedName = $this->fileNmae . '_' . $this->generatedSlug;

        File::makeDirectory($this->uf . $this->generatedName);
        Zipper::make($this->uf . "/" . $fileName . self::ZIP)->extractTo($this->uf . $this->generatedName . DS);
    }

    /**
     * @param $data
     * @return array|string
     */
    public function validatConfAndMoveToMain($data)
    {
        $this->folder = $data['folder'];
        $this->name = $data['data'];
        $this->slug = $data['slug'];
        $this->place = $data['place'];
        if (File::exists($this->uf . $this->folder . DS . 'config.json')) {
            $file = $this->uf . $this->folder . DS . 'config.json';

            $response = $this->validate($file);
            if ($response['error'])
                return $response;
            $this->dir = base_path($this->path . $this->folder);
            File::copyDirectory($this->uf . $this->folder, $this->dir);
            return $response;
        } else {
            if (File::exists($this->uf . $this->folder . DS . $this->name . DS . 'config.json')) {
                $file = $this->uf . $this->folder . DS . $this->name . DS . 'config.json';
                $response = $this->validate($file);
                if ($response['error'])
                    return $response;

                $this->dir = base_path($this->path . $this->folder);
                File::copyDirectory($this->uf . $this->folder . DS . $this->name, $this->dir);
                return $response;
            }
        }
        return $this->uf . $this->folder . DS . 'config.json';
    }

    /**
     * @param $file
     * @return array
     */
    private function validate($file)
    {
        $conf = File::get($file);
        if ($conf) {
            $conf = json_decode($conf, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                return ['message' => 'Unable to open config json file: ' . json_last_error_msg(), 'code' => '404', 'error' => true];
            }

            //check rquired key in config json file
            $required_keys = $this->gear['required_keys'];
            $result = array_intersect_key($required_keys, $conf);
            $messageRequired = implode(',', $required_keys);
            if (count($result) != count($required_keys))
                return ['message' => 'keys are required: ' . $messageRequired, 'code' => '404', 'error' => true];

            //check equal keys in config json file
            $equal_keys = $this->gear['equal_keys'];
            $result = array_intersect_assoc($conf, $equal_keys);
            $messageEqual = implode(',', $equal_keys);
            if (count($result) != count($equal_keys))
                return ['message' => 'keys are not equal: ' . $messageEqual, 'code' => '404', 'error' => true];

            $conf['slug'] = (string)$this->slug;
            $conf['folder'] = (string)$this->folder;
            $conf['created_at'] = time('now');
            $conf['is_core'] = 0;
            $conf['place'] = $this->place;
            $json = json_encode($conf, true);
            File::put($file, $json);
            return ['data' => $conf, 'code' => '200', 'error' => false];
        }

        return ['message' => 'Json file is empty !!!', 'code' => '404', 'error' => true];
    }

    /**
     * @param $data
     * @return array
     */
    public function makeVariations($data)
    {
        $variation_path = $this->dir . '/variations';
        if (File::isDirectory($variation_path)) {
            if ($files = File::allFiles($variation_path)) {
                foreach ($files as $file) {
                    if (File::extension($file) == 'json') {
                        $json = File::get($file);
                        if ($json) {
                            $json = json_decode($json, true);
                            $json['id'] = $data['slug'] . '.' . uniqid();
                            File::put($variation_path . DS . $json['id'] . '.json', json_encode($json, true));
                            File::delete($file);
                        }
                    }
                }
                return ['code' => '200', 'error' => false];
            }
        }
        File::makeDirectory($variation_path);
        $model = $this->gear['model'];
        $model_variation = $this->gear['variation'];
        $gearObject = $model::find($data['slug']);
        $variation = new $model_variation();

        return $variation->createVariation($gearObject, ['title' => 'Default'])->save();
    }

    public function registerField($rootPath, $variation)
    {
        if (File::exists($rootPath . DS . 'config.json')) {
            $config = json_decode(File::get($rootPath . DS . 'config.json'), true);
            if (isset($config['type']) && $config['type'] == 'fields' && File::exists($rootPath . DS . 'field.json')) {
                $fieldData = json_decode(File::get($rootPath . DS . 'field.json'), true);
                if ($this->validateFieldJsonFile($fieldData)) {
                    $field = new Fields;
                    $field->name = $fieldData['name'];
                    $field->table_name = $fieldData['table_name'];
                    $field->column_name = $fieldData['column_name'];
                    $field->type = $fieldData['type'];
                    $field->unit = $variation->id;
                    $field->structured_by = 'plugin';
                    $field->created_by = NULL;
                    $field->json_data = json_encode($fieldData['data']);
                    $field->slug = uniqid();
                    $field->save();
                }
            }
        }

    }

    public function validateFieldJsonFile($fieldData)
    {
        if (!empty($fieldData)) {
            return isset($fieldData['name']) && isset($fieldData['table_name']) && isset($fieldData['column_name']) && isset($fieldData['type']);
        }
        return false;
    }

}