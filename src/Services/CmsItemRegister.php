<?php
/**
 * Created by PhpStorm.
 * User: Ara Arakelyan
 * Date: 1/24/2017
 * Time: 5:50 PM
 */

namespace Btybug\btybug\Services;

use File;

/**
 * Class CmsItemRegistrator
 * @package Btybug\Modules\Models\Models
 */
class CmsItemRegister
{

    const EXT = '.json';
    private static $gearsSubDirectoryFileStructure = [
        'Unit' => 'Units',
        'HF' => 'HF',
        'Section' => 'Sections',
        'PageSection' => 'PageSections',
        'main_body' => 'MainBody'
    ];
    private static $gearsSelfTypeStructure = [
        'units' => 'Units',
        'HF' => 'HF',
        'sections' => 'Sections',
        'page_sections' => 'PageSections',
        'main_body' => 'MainBody'
    ];
    private static $places = [
        'FrontGears' => 'frontend',
        'BackGears' => 'backend',
    ];
    /**
     * @var string
     */
    private static $configDirectoryName = 'Config';
    /**
     * @var string
     */
    private static $gearsDirectoryName = 'Gears';
    /**
     * @var string
     */
    private static $gearsBackSubDirectoryName = 'BackGears';
    /**
     * @var string
     */
    private static $gearsFrontSubDirectoryName = 'FrontGears';
    /**
     * @var string
     */
    private static $gearsUnitSubDirectoryName = 'Units';
    /**
     * @var string
     */
    private static $configFileName = 'config.json';
    private static $place = 'frontend';
    /**
     * @var string
     */
    private static $gearsConfigFileName = 'gears.json';
    private static $unitsConfigFileName = 'units.json';
    private static $hfConfigFileName = 'hf.json';
    private static $sectionsConfigFileName = 'sections.json';
    private static $page_sectionsConfigFileName = 'page_sections.json';
    private static $main_bodyConfigFileName = 'main_body.json';

    /**
     * @param $absolutePath
     * @param $relativePath
     * @param $moduleData
     */
    public static function run($absolutePath, $relativePath, $moduleData)
    {
        if (self::checkConfigDirectory($absolutePath)) {
            if (self::checkGearsDirectory($absolutePath)) {
                if (self::checkGearsFrontSubDirectory($absolutePath)) {
                    self::setPlace(self::$gearsFrontSubDirectoryName);
                    self::registerModuleGears($absolutePath, $relativePath, self::$gearsFrontSubDirectoryName, $moduleData);
//                    self::registerModuleUnits($absolutePath, $relativePath, self::$gearsFrontSubDirectoryName, $moduleData);
                }
                if (self::checkGearsBackSubDirectory($absolutePath)) {
                    self::setPlace(self::$gearsBackSubDirectoryName);
                    self::registerModuleGears($absolutePath, $relativePath, self::$gearsBackSubDirectoryName, $moduleData);
//                    self::registerModuleUnits($absolutePath, $relativePath, self::$gearsBackSubDirectoryName, $moduleData);
                }
            }
        }
    }

    /**
     * @param $path
     * @return bool
     */
    private static function checkConfigDirectory($path)
    {
        if (File::isDirectory($path)) {
            $configDirectoryPath = $path . DS . self::$configDirectoryName;
            if (File::isDirectory($configDirectoryPath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $path
     * @return bool
     */
    private static function checkGearsDirectory($path)
    {
        $gearsDirectoryPath = $path . DS . self::$configDirectoryName . DS . self::$gearsDirectoryName;
        if (File::isDirectory($gearsDirectoryPath)) {
            return true;
        }
        return false;
    }

    /**
     * @param $path
     * @return bool
     */
    private static function checkGearsFrontSubDirectory($path)
    {
        $gearsDirectoryPath = $path . DS . self::$configDirectoryName . DS . self::$gearsDirectoryName;
        $subDirectoryPaths = File::directories($gearsDirectoryPath);
        $subDirectoryNames = [];
        foreach ($subDirectoryPaths as $dirPath) {
            if (File::isDirectory($dirPath)) {
                $subDirectoryNames[] = basename($dirPath);
            }
        }
        return in_array(self::$gearsFrontSubDirectoryName, $subDirectoryNames) ? true : false;
    }

    private static function setPlace($gearsFrontSubDirectoryName)
    {
        self::$place = $gearsFrontSubDirectoryName == self::$gearsBackSubDirectoryName ? 'backend' : 'frontend';
    }


    private static function registerModuleGears($path, $relativePath, $type, $moduleData)
    {
        $result = [];
        foreach (self::$gearsSelfTypeStructure as $method => $dir) {
            $gearDirectoryPath = $path . DS . self::$configDirectoryName . DS . self::$gearsDirectoryName
                . DS . $type . DS . $dir;
            if (File::isDirectory($gearDirectoryPath)) {
                $subDirectoryPaths = File::directories($gearDirectoryPath);
                if (!empty($subDirectoryPaths)) {
                    foreach ($subDirectoryPaths as $dirPath) {
//                        $methodToCall = 'register' . $method;
//                        self::$methodToCall($dirPath, $relativePath, $type, $moduleData);
                        self::registerGear($dirPath, $relativePath, $type, $method, $moduleData);
                    }
                }
            }
        }

        //dd($unitSubDirectoryPath);


        return in_array("false", $result) ? false : true;
    }

    public static function registerGear($path, $relativePath, $type, $gearType = NULL, $moduleData = NULL)
    {

        if ($gearType && File::isDirectory($path)) {

            $configJsonFile = $path . DS . self::$configFileName;
            if (File::isFile($configJsonFile)) {
                $file = File::get($configJsonFile);
                $configFileData = json_decode($file, true);
                $configFileData['slug'] = $moduleData ? uniqid() : $configFileData['slug'];
                $configFileData['folder'] = isset($configFileData['folder']) ? $configFileData['folder'] : basename($path);
                $configFileData['created_at'] = isset($configFileData['created_at']) ? $configFileData['created_at'] : time('now');
                $configFileData['place'] = isset($configFileData['place']) ? $configFileData['place'] : self::$places[$type];
                $configFileData['path'] = (($moduleData ? $relativePath . DS . self::$configDirectoryName . DS
                    . self::$gearsDirectoryName . DS . $type . DS . self::$gearsSelfTypeStructure[$gearType]
                    . DS . basename($path) : $relativePath));
                $configFileData['path'] = str_replace('\\', '/', $configFileData['path']);
                if ($moduleData && isset($moduleData['slug'])) {
                    $configFileData['module_slug'] = $moduleData['slug'];
                }
                $configFileData['is_core'] = $moduleData ? 1 : 0;
                $configFileName = strtolower($gearType) . self::EXT;
                $storageConfigContainerFile = storage_path('app' . DS . $configFileName);
                if (!File::isFile($storageConfigContainerFile)) {
                    $dataToInsert = [
                        $configFileData['slug'] => $configFileData
                    ];
                } else {
                    $existingData = File::get(storage_path('app' . DS . $configFileName));
                    $existingDataDecoded = json_decode($existingData, true);
                    $isUnique = self::isUnique($existingDataDecoded, $configFileData);
                    if (!$isUnique) {
                        $path=base_path($configFileData['path']);
                        if(File::isDirectory($path)){
                            File::deleteDirectory($path);
                        }
                        return false;
                    }
                    $existingDataDecoded[$configFileData['slug']] = $configFileData;
                    $dataToInsert = $existingDataDecoded;
                }


                $configFileData = json_encode($configFileData, true);
                if (File::put($configJsonFile, $configFileData)
                    && File::put(storage_path('app' . DS . $configFileName), json_encode($dataToInsert, true))) {
                    return true;
                }
            }
        }
    }

    /**
     * @param $path
     * @param $relativePath
     * @param $type
     * @param $moduleData
     * @return bool
     */
    //TODO change conditions from if to switch case, change json file paths
    /**
     * @param $path
     * @return bool
     */
    private static function checkGearsBackSubDirectory($path)
    {
        $gearsDirectoryPath = $path . DS . self::$configDirectoryName . DS . self::$gearsDirectoryName;
        if (File::isDirectory($gearsDirectoryPath)) {
            $subDirectoryPaths = File::directories($gearsDirectoryPath);
            $subDirectoryNames = [];
            foreach ($subDirectoryPaths as $dirPath) {
                if (File::isDirectory($dirPath)) {
                    $subDirectoryNames[] = basename($dirPath);
                }
            }
            return in_array(self::$gearsBackSubDirectoryName, $subDirectoryNames) ? true : false;
        }
        return false;
    }

    public static function registerUnit($path, $relativePath, $type, $moduleData = NULL)
    {
        if (File::isDirectory($path)) {
            $configJsonFile = $path . DS . self::$configFileName;
            if (File::isFile($configJsonFile)) {
                $file = File::get($configJsonFile);
                $configFileData = json_decode($file, true);
                if (isset($configFileData['self_type']) && $configFileData['self_type'] == 'units') {
                    $configFileData['slug'] = $moduleData ? uniqid() : $configFileData['slug'];
                    $configFileData['path'] = $moduleData ? $relativePath . DS . self::$configDirectoryName . DS
                        . self::$gearsDirectoryName . DS . $type . DS . self::$gearsSubDirectoryFileStructure['Unit']
                        . DS . basename($path) : $relativePath;
                    if ($moduleData && isset($moduleData['slug'])) {
                        $configFileData['module_slug'] = $moduleData['slug'];
                    }
                    $configFileData['place'] = isset($configFileData['place']) ? $configFileData['place'] : self::getPlace();
                    $configFileData['folder'] = isset($configFileData['folder']) ? $configFileData['folder'] : basename($path);
                    $configFileData['created_at'] = isset($configFileData['created_at']) ? $configFileData['created_at'] : time('now');
                    $configFileData['is_core'] = $moduleData ? 1 : 0;
                    $storageConfigContainerFile = storage_path('app' . DS . self::$unitsConfigFileName);
                    if (!File::isFile($storageConfigContainerFile)) {
                        $dataToInsert = [
                            $configFileData['slug'] => $configFileData
                        ];
                    } else {
                        $existingData = File::get(storage_path('app' . DS . self::$unitsConfigFileName));
                        $existingDataDecoded = json_decode($existingData, true);
                        $existingDataDecoded[$configFileData['slug']] = $configFileData;
                        $dataToInsert = $existingDataDecoded;

                    }
                    if (File::put(storage_path('app' . DS . self::$unitsConfigFileName), json_encode($dataToInsert, true))) {
                        return true;
//                        array_push($result, "true");
                    }
                }
            }
        }
    }

    private static function getPlace()
    {
        return self::$place;
    }

    public static function registerHF($path, $relativePath, $type, $moduleData = NULL)
    {
        if (File::isDirectory($path)) {
            $configJsonFile = $path . DS . self::$configFileName;
            if (File::isFile($configJsonFile)) {
                $file = File::get($configJsonFile);
                $configFileData = json_decode($file, true);
                if (isset($configFileData['self_type']) && $configFileData['self_type'] == 'HF'
                    && (isset($configFileData['type']) && $configFileData['type'] == 'header')
                    || (isset($configFileData['type']) && $configFileData['type'] == 'footer')
                ) {
                    $configFileData['slug'] = $moduleData ? uniqid() : $configFileData['slug'];
                    $configFileData['path'] = $moduleData ? $relativePath . DS . self::$configDirectoryName . DS
                        . self::$gearsDirectoryName . DS . $type . DS . self::$gearsSubDirectoryFileStructure['HF']
                        . DS . basename($path) : $relativePath;
                    if ($moduleData && isset($moduleData['slug'])) {
                        $configFileData['module_slug'] = $moduleData['slug'];
                    }
                    $configFileData['is_core'] = $moduleData ? 1 : 0;
                    $storageConfigContainerFile = storage_path('app' . DS . self::$hfConfigFileName);
                    if (!File::isFile($storageConfigContainerFile)) {
                        $dataToInsert = [
                            $configFileData['slug'] => $configFileData
                        ];
                    } else {
                        $existingData = File::get(storage_path('app' . DS . self::$hfConfigFileName));
                        $existingDataDecoded = json_decode($existingData, true);
                        $existingDataDecoded[$configFileData['slug']] = $configFileData;
                        $dataToInsert = $existingDataDecoded;

                    }
                    if (File::put(storage_path('app' . DS . self::$hfConfigFileName), json_encode($dataToInsert, true))) {
                        return true;
//                        array_push($result, "true");
                    }
                }
            }
        }
    }

    public static function registerSection($path, $relativePath, $type, $moduleData = NULL)
    {
        if (File::isDirectory($path)) {
            $configJsonFile = $path . DS . self::$configFileName;
            if (File::isFile($configJsonFile)) {
                $file = File::get($configJsonFile);
                $configFileData = json_decode($file, true);
                if (isset($configFileData['self_type']) && $configFileData['self_type'] == 'section') {
                    $configFileData['slug'] = $moduleData ? uniqid() : $configFileData['slug'];
                    $configFileData['path'] = $moduleData ? $relativePath . DS . self::$configDirectoryName . DS
                        . self::$gearsDirectoryName . DS . $type . DS . self::$gearsSubDirectoryFileStructure['Section']
                        . DS . basename($path) : $relativePath;
                    if ($moduleData && isset($moduleData['slug'])) {
                        $configFileData['module_slug'] = $moduleData['slug'];
                    }
                    $configFileData['is_core'] = $moduleData ? 1 : 0;
                    $storageConfigContainerFile = storage_path('app' . DS . self::$sectionsConfigFileName);
                    if (!File::isFile($storageConfigContainerFile)) {
                        $dataToInsert = [
                            $configFileData['slug'] => $configFileData
                        ];
                    } else {
                        $existingData = File::get(storage_path('app' . DS . self::$sectionsConfigFileName));
                        $existingDataDecoded = json_decode($existingData, true);
                        $existingDataDecoded[$configFileData['slug']] = $configFileData;
                        $dataToInsert = $existingDataDecoded;

                    }
                    if (File::put(storage_path('app' . DS . self::$sectionsConfigFileName), json_encode($dataToInsert, true))) {
                        return true;
//                        array_push($result, "true");
                    }
                }
            }
        }
    }

    public static function registerPageSection($path, $relativePath, $type, $moduleData = NULL)
    {
        if (File::isDirectory($path)) {
            $configJsonFile = $path . DS . self::$configFileName;
            if (File::isFile($configJsonFile)) {
                $file = File::get($configJsonFile);
                $configFileData = json_decode($file, true);
                if (isset($configFileData['self_type']) && $configFileData['self_type'] == 'page_section') {
                    $configFileData['slug'] = $moduleData ? uniqid() : $configFileData['slug'];
                    $configFileData['path'] = $moduleData ? $relativePath . DS . self::$configDirectoryName . DS
                        . self::$gearsDirectoryName . DS . $type . DS . self::$gearsSubDirectoryFileStructure['PageSection']
                        . DS . basename($path) : $relativePath;
                    if ($moduleData && isset($moduleData['slug'])) {
                        $configFileData['module_slug'] = $moduleData['slug'];
                    }
                    $configFileData['is_core'] = $moduleData ? 1 : 0;
                    $storageConfigContainerFile = storage_path('app' . DS . self::$page_sectionsConfigFileName);
                    if (!File::isFile($storageConfigContainerFile)) {
                        $dataToInsert = [
                            $configFileData['slug'] => $configFileData
                        ];
                    } else {
                        $existingData = File::get(storage_path('app' . DS . self::$page_sectionsConfigFileName));
                        $existingDataDecoded = json_decode($existingData, true);
                        $existingDataDecoded[$configFileData['slug']] = $configFileData;
                        $dataToInsert = $existingDataDecoded;

                    }
                    if (File::put(storage_path('app' . DS . self::$page_sectionsConfigFileName), json_encode($dataToInsert, true))) {
                        return true;
//                        array_push($result, "true");
                    }
                }
            }
        }
    }

    public static function isUnique($coreData, $unitData)
    {
        $needle = $unitData['title'] . $unitData['author'];
        foreach ($coreData as $existing) {
            $delta = $existing['title'] . $existing['author'];

            if ($delta == $needle) {
                echo $delta .'=='. $needle;
                return false;
            }
        }
        return true;
    }
}