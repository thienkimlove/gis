<?php
namespace Gis\Models\Services;

use Gis\Models\Entities\FertilityMap;
use Gis\Models\Entities\FertilityMapExtend;
use Gis\Models\Entities\FolderLayer;
use Gis\Models\Entities\User;
use Gis\Models\MapTools;
use Gis\Models\Repositories\FertilityMapFacade;
use Gis\Models\Repositories\FolderFacade;
use Illuminate\Pagination\LengthAwarePaginator;
use Gis\Models\Repositories\UserFacade;
use Gis\Exceptions\GisException;
use Gis\Models\SystemCode;
use Illuminate\Support\Facades\DB;
use Gis\Models\Repositories\FertilityMapSelectionFacade;

class FertilityMapService extends BaseService implements FertilityMapServiceInterface
{

    const NUMBER_OF_COLUMN = 6;

    const PATH_UPLOAD = 'uploads';

    const MAP_IPP = 10;

    const PRICE = 0;

    const MAP_TYPE = true;

    const IS_PAID = true;

    const DEFAULT_LAYER_ID = null;

    const MAX_COLOR = 11;

    private $baseService;

    private $resultDataOfCSV = [];

    private $coordinateSystemNumber;

    private $geometryService;

    private $folderService;

    private $SIRD;

    private $legend = [];

    const FIRST_COLUMN = 0;

    const ONLY_ONE_COLUMN = 1;

    const MAP_NAME = 'Fertility Map';

    const MAP_SIZE = '2000 2000';

    /**
     * The constructor to initialize a new instance of FertilityMapService
     *
     * @param BaseService $baseService            
     * @param GeometryService $geometryService            
     * @param FolderService $folderService            
     */
    function __construct(BaseService $baseService, GeometryService $geometryService, FolderService $folderService)
    {
        $this->baseService = $baseService;
        $this->geometryService = $geometryService;
        $this->folderService = $folderService;
    }

    /**
     * Import fertility map into database and also create a new fertility map
     *
     * @param $file the
     *            uploaded csv file
     * @param $userId the
     *            destination user of fertility map
     * @param $fileName the
     *            file name of fertility map
     * @param $folder_id the
     *            parent folder of fertility map layer
     * @return array|bool
     * @throws \Exception
     */
    public function importLayer($file, $userId, $fileName, $folder_id)
    {
        if (empty($file) || !$file->isValid()) {
            return false;
        }

        if ($this->checkImportLayerDataExists($fileName,$userId )) {
            return [
                'message' => trans('common.folder_create_exists')
            ];
        }
        $valid = $this->validateContentFileCSV($file->getRealPath());
        if (!$valid) {
            return false;
        }
        $user = User::find($userId);
        if (empty($user)) {
            return ['message' => trans('common.changinguser_user_not_exists')];
        }
        $folder = FolderLayer::find($folder_id);

        if (empty($folder)) {
            return ['message' => trans('common.upload_layer_not_exist')];
        }

        return $this->createFertilityMap($userId, $fileName, $folder_id);

    }
    /**
     * Check Import Layer Name Data exists In DB
     *
     * @param String $field
     * @param String $value
     * @param $operator
     * @return boolean
     */
    private function checkImportLayerDataExists($fileName,$userId)
    {
        $existDatas=FertilityMap::where('user_id',$userId)->get();
        foreach($existDatas as $existData){
            $layer = FolderFacade::findById($existData->layer_id);
            if(strtolower($layer->name)== strtolower($fileName)) return true;
        }
        if(FolderFacade::selectModel()->where('name','ilike',$fileName)->whereNotNull('scale_type')->whereNotNull('parent_folder')->first()) return true;
        return false;
    }
    /**
     * Process to retrieve map info and display the map to the browser
     *
     * @param $mapId the
     *            fertility map id to be displayed
     * @return array
     */
    public function showMapList($mapId)
    {
        $tmp = FertilityMapExtend::where('layer_id', '=', $mapId)->get();
        if (empty($tmp[0])) {
            return [];
        }
        $result = $tmp[0];
        return array(
            'extent' => array(
                (float) $result->extend_x1,
                (float) $result->extend_y1,
                (float) $result->extend_x2,
                (float) $result->extend_y2
            ),
            'file_name' => $result->map_name,
            'layers' => $result->layers,
            'central' => array(
                (float) $result->central_point_x1,
                (float) $result->central_point_y1
            )
        );
    }

    /**
     * Validate fertility map csv file
     *
     * @param $file the
     *            csv file to validated
     * @return bool it indicates the file data is valid or not
     */
    function validateContentFileCSV($file)
    {
        $handle = fopen($file, "r");
        fwrite($handle, $file);
        fseek($handle, 0);
        ini_set('auto_detect_line_endings', true);
        $row = 0;
        $results = [];
        
        while (($rows = fgetcsv($handle, ",")) !== false) {
            
            if (! $this->countColumn($row, $rows) || ! $this->validateRowWithCode($row, $rows)) {
                return false;
            }
            if ($row > 0) {
                
                foreach ($rows as $value) {
                    if (! $this->validateInteger($value)) {
                        return false;
                    }
                }
                $results[] = $rows;
            }
            $row ++;
        }
        $this->resultDataOfCSV = $results;
        ini_set('auto_detect_line_endings', false);
        return true;
    }

    /**
     * Validate System number inside fertility csv file
     *
     * @param
     *            $rows
     * @return bool it indicates the file data is valid or not
     */
    function validateCoordinateSystemNumber($rows)
    {
        if (is_array($rows)) {
            if (count($rows) == self::ONLY_ONE_COLUMN) {
                return $this->validateInteger($rows[0]);
            }
            return $this->validateInteger($rows[0]) && $this->validateOtherValueOfCode($rows);
        }
        
        return false;
    }

    /**
     * Validate other data inside fertility csv file
     *
     * @param
     *            $val
     * @return bool it indicates the file data is valid or not
     */
    function validateOtherValueOfCode($val)
    {
        return empty($val[1]) && empty($val[2]) && empty($val[3]) && empty($val[4]) && empty($val[5]);
    }

    /**
     * validate the input data is integer or not
     *
     * @param
     *            $number
     * @return bool it indicates the file data is valid or not
     */
    function validateInteger($number)
    {
        return (bool) preg_match('/^[\-+]?[0-9]+$/', $number);
    }

    /**
     * Create all the data for a new fertility map
     *
     * @param $userId the
     *            id of destination user
     * @param $fileName the
     *            uploaded csv file
     * @param $folder_id the
     *            parent folder of fertility layer
     * @return int
     * @throws \Exception
     */
    public function createFertilityMap($userId, $fileName, $folder_id)
    {
        DB::beginTransaction();
        try {
            $folderLayer = [];
            $folderLayer['name'] = $fileName;
            $folderLayer['is_recyclebin'] = false;
            $folderLayer['is_terrain_folder'] = false;
            $folderLayer['is_fertility_folder'] = false;
            $folderLayer['is_admin_folder'] = true;
            $folderLayer['is_fertilizer_folder'] = false;
            $folderLayer['parent_folder'] = $folder_id;
            $folderLayer['order_number'] = $this->folderService->getMaxFolderLayerOrder() + 1;
            $folderLayer = $this->modifyData($folderLayer, true);
            $folderLayer = FolderLayer::create($folderLayer);
            $ids[] = $folderLayer->id;
            // 1-N
            $fertilityMap = [];
            $fertilityMap['user_id'] = $userId;
            $fertilityMap['layer_id'] = $folderLayer->id;
            $fertilityMap['coordinates_system_number'] = $this->coordinateSystemNumber;
            $fertilityMap = $this->modifyData($fertilityMap, true);
            $fertilityMap = FertilityMap::create($fertilityMap);
            $ids[] = $fertilityMap->id;
            // 1-N
            $listMap = [];
            foreach ($this->resultDataOfCSV as $result) {
                $mapInfo = [];
                $mapInfo['geo'] = $this->makeGeo($result, $this->SIRD);
                $mapInfo = $this->modifyData($mapInfo, true);
                $mapInfo['nitrogen'] = $result[5] < self::MAX_COLOR ? $result[5] : self::MAX_COLOR;
                $this->legend[] = $result[5];
                $mapInfo['fertility_id'] = $fertilityMap->id;
                $mapInfo['fertilization_classification_code'] = $result[4];
                $listMap[] = $mapInfo;
            }
            DB::table('fertility_map_infos')->insert($listMap);
            // recreate user map file again.
            MapTools::mapFile($userId);
            DB::commit();
            return count($ids);
        } catch (\Exception $ex) {
            DB::rollBack();
            // rethrow exception and it'll be handled in exception handling of application
            throw new GisException(trans('common.lbl_import_layer_map_with_file_csv_error'));
        }
    }

    /**
     * Convert the long&let coordinate to geometry data type
     *
     * @param
     *            $rows
     * @param
     *            $srid
     * @return mixed
     */
    public function makeGeo($rows, $srid)
    {
        return $this->geometryService->makeRectangleGeometry($rows[0], $rows[1], $rows[2], $rows[3], $srid);
    }

    /**
     * Create a fertility map file
     *
     * @param $userId the
     *            destination user
     * @param $extension the
     *            extension of map file
     * @return string
     */
    protected function createFileName($userId, $extension)
    {
        return time() . $userId . '.' . strtolower($extension);
    }

    /**
     * define function Filter users with conditions
     *
     * @param array() $postData            
     * @param array() $pagingRequest            
     *
     */
    public function filterFertilityMap($postData, $pagingRequest)
    {
        $conditionCount = 0;
        
        foreach ($postData as $key => $value) {
            $operator = '=';
            
            if ($key === '_token' || empty($value)) {
                continue;
            } elseif ($key === 'user_id') {
                $value = $value === 'f' ? false : true;
            } elseif ($key === 'map_name') {
                $operator = 'like';
                $value = "%" . $value . "%";
            }
            $eloquent = FertilityMap::where($key, $operator, $value);
            $conditionCount ++;
        }
        
        if (! $conditionCount) {
            return $this->getAllFertilityMap(null, $pagingRequest);
        }
        
        $users = $eloquent->where('layer_id', '=', null)
            ->orderBy('id', 'desc')
            ->paginate(self::MAP_IPP);
        $response = $this->buildResponseFilter($users, $pagingRequest['page']);
        
        return $response;
    }

    /**
     * Get all the fertility map for current user
     *
     * @param null $limit            
     * @param null $pagingRequest            
     * @return aray
     */
    public function getAllFertilityMap($limit = null, $pagingRequest = null)
    {
        $limit = empty($limit) ? self::MAP_IPP : $limit;
        $usermaps = FertilityMap::orderBy('id', 'desc')->where('layer_id', '=', null)->paginate($limit);
        $currentPage = $pagingRequest ? $pagingRequest['page'] : 1;
        $response = $this->buildResponseFilter($usermaps, $currentPage);
        
        return $response;
    }

    /**
     * Build response data to filteer users
     *
     * @param LengthAwarePaginator $dataPaging            
     * @param int $currentPage            
     * @return aray() $response
     */
    public function buildResponseFilter(LengthAwarePaginator $dataPaging, $currentPage)
    {
        $results = array();
        if (! $dataPaging->isEmpty()) {
            foreach ($dataPaging as $usermap) {
                $tmpMap = [
                    'id' => $usermap->id,
                    'mapName' => $usermap->map_name,
                    'userName' => $usermap->user->username
                ];
                array_push($results, $tmpMap);
            }
        }
        $response = array(
            'page' => $dataPaging->currentPage(),
            'total' => $dataPaging->lastPage(),
            'records' => $dataPaging->total(),
            'rows' => $results
        );
        
        return $response;
    }

    /**
     * Delete fertility map
     *
     * @param $ids the
     *            fertility map id
     * @return int
     */
    function deleteFertilityMap($ids)
    {
        FertilityMap::whereIn('id', $ids)->delete();
        return count($ids);
    }

    /**
     * Validate the system number of fertility map
     *
     * @param
     *            $row
     * @param
     *            $rows
     * @return bool
     */
    function validateRowWithCode($row, $rows)
    {
        if ($row == self::FIRST_COLUMN) {
            if (! $this->validateCoordinateSystemNumber($rows)) {
                return false;
            }
            $this->coordinateSystemNumber = $rows[0];
            $this->SIRD = $this->reflectSRID($rows[0]);
        }
        
        return true;
    }

    /**
     * Count the number of columns
     *
     * @param
     *            $row
     * @param
     *            $rows
     * @return bool
     */
    public function countColumn($row, $rows)
    {
        if (self::FIRST_COLUMN == $row) {
            return true;
        }
        return count($rows) == self::NUMBER_OF_COLUMN;
    }

    /**
     * Get All admin's map
     *
     * @return array() Gis\Models\Entities\FertilityMap
     */
    public function getAllAdminMaps()
    {
        $users = UserFacade::all();
        $resultMaps = array();
        foreach ($users as $user) {
            if (! $user->usergroup->auth_authorization) {
                continue;
            }
            
            $userMaps = $user->fertility_maps;
            
            if ($userMaps) {
                foreach ($userMaps as $map) {
                    $resultMaps[$map->folderLayer->id] = $map->folderLayer->name;
                }
            }
        }
        if (! empty($resultMaps)) {
            asort($resultMaps);
        }
        
        $resultMaps = array(
            '' => trans('common.administrator_export_map_default_value')
        ) + $resultMaps;
        return $resultMaps;
    }

    /**
     * Define the system number for of application
     *
     * @param
     *            $srid
     * @return mixed
     */
    public function reflectSRID($srid)
    {
        $array = [
            '1'=> 2443,
            '2'=> 2444,
            '3'=> 2445,
            '4'=> 2446,
            '5'=> 2447,
            '6'=> 2448,
            '7'=> 2449,
            '8'=> 2450,
            '9'=> 2451,
            '10'=> 2452,
            '11' => 2453,
            '12' => 2454,
            '13' => 2455
        ];
        
        return $array[$srid];
    }

    /**
     * Find fertility map by Id
     *
     * @param int $id            
     */
    function findById($id)
    {
        $resource = FertilityMapFacade::findById($id);
        
        if (empty($resource))
            throw new GisException(trans('common.fertility_map_not_found'), SystemCode::NOT_FOUND);
        
        return $resource;
    }

    /**
     * Get fertility map selection when spcification fertilzier map
     * get by crop & fertility map & specifix user
     *
     * @param int $cropId            
     * @param int $fertilityMapId            
     * @param int $userId            
     *
     * @return
     *
     */
    public function getMapSelection($cropId, $fertilityMapId, $userId)
    {
        $fertilityMapSelection = array();
        $conditions = array(
            'user_id' => $userId,
            'fertility_map_id' => $fertilityMapId,
            'crops_id' => $cropId,
            'is_ready' => true,
            'ins_time' => 1
        );
        
        $mapCollection = FertilityMapSelectionFacade::filterByInfo($conditions);
        if (! $mapCollection->isEmpty()) {
            foreach ($mapCollection as $id => $insTime) {
                $fertilityMapSelection[$id] = $insTime;
            }
        }
        
        return $fertilityMapSelection;
    }
}