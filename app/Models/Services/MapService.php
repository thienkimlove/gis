<?php
namespace Gis\Models\Services;

use Carbon\Carbon;
use Gis\Helpers\Stopwatch;
use Gis\Models\Entities\SystemFertilizerDefinitionDetailNito;
use Gis\Models\GeoTools;
use Gis\Models\MapTools;
use Gis\Models\Repositories\FertilityMapSelectionFacade;
use Gis\Models\Repositories\FertilityMapSelectionInfoFacade;
use Gis\Models\Repositories\FertilizerFacade;
use Gis\Exceptions\GisException;
use Gis\Models\Repositories\FertilizerMapInfoFacade;
use Gis\Models\SystemCode;
use Gis\Models\Repositories\StandardCropFacade;
use Gis\Models\Repositories\CropFacade;
use Gis\Models\Entities\Crop;
use Illuminate\Support\Facades\DB;
use Gis\Models\Repositories\MapColorFacade;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;
use Gis\Models\Entities\FertilizerMap;
use Gis\Models\Entities\FertilizerMapProperty;
use Gis\Models\Repositories\FertilizerMapFacade;
use Gis\Models\Entities\StandardCropNito;
use Gis\Models\Repositories\FertilityMapFacade;

/**
 * Methods to work with repositories.
 * Class MapService
 *
 * @package Gis\Models\Services
 */
class MapService extends BaseService implements MapServiceInterface
{

    /**
     * @const PREFIX_DIVISION_AMOUNT
     */
    const PREFIX_DIVISION_AMOUNT = 'division_amount';

    /**
     * @const ORGANIC_MATTER_BYPRODUCT
     */
    const ORGANIC_MATTER_BYPRODUCT = 1;

    /**
     * @const ORGANIC_MATTER_GREEN_MANURE
     */
    const ORGANIC_MATTER_GREEN_MANURE = 2;

    /**
     * @const ORGANIC_MATTER_COMPOST
     */
    const ORGANIC_MATTER_COMPOST = 3;

    /**
     * @const ORGANIC_MATTER_Other
     */
    const ORGANIC_MATTER_OTHER = 4;

    /**
     * @const ORGANIC_MATTER_TOTAL
     */
    const ORGANIC_MATTER_TOTAL = 5;

    /**
     * @const MACHINE_TWO_TYPE__MAIN
     */
    const MACHINE_TWO_TYPE_MAIN = 'main';

    /**
     * @const MACHINE_TWO_TYPE__SUB
     */
    const MACHINE_TWO_TYPE_SUB = 'sub';

    /**
     * @const FERTILIZER_MACHINE_ONE
     */
    const FERTILIZER_MACHINE_ONE = 1;

    /**
     * @const FERTILIZER_MACHINE_TWO
     */
    const FERTILIZER_MACHINE_TWO = 2;

    /**
     * @const SOIL_ANALYSIS_STANDARD_RATIO
     */
    const SOIL_ANALYSIS_STANDARD_RATIO = 1;

    /**
     * @const SOIL_ANALYSIS_ENABLE
     */
    const SOIL_ANALYSIS_ENABLE = 2;

    /**
     * @const SOIL_ANALYSIS_DISABLE
     */
    const SOIL_ANALYSIS_DISABLE = 1;

    /**
     * @const FERTILIZER_STANDARD_ADMIN
     */
    const FERTILIZER_STANDARD_ADMIN = 1;

    /**
     * @const FERTILIZER_STANDARD_SYSTEM
     */
    const FERTILIZER_STANDARD_SYSTEM = 0;

    /**
     * @const FERTILIZER_STANDARD_SYSTEM
     */
    const FERTILIZER_STANDARD_USER = 2;

    /**
     * @const TYPE_NITO
     */
    const TYPE_NITO = 'n';

    /**
     * @const TYPE_KALI
     */
    const TYPE_KALI = 'k';

    /**
     * @const TYPE_PHOTPHO
     */
    const TYPE_PHOTPHO = 'p';

    /**
     * @const MACHINE_ONE_N
     */
    const MACHINE_ONE_N = 1;

    /**
     * @const MACHINE_ONE_N_P
     */
    const MACHINE_ONE_N_P = 2;

    /**
     * @const MACHINE_ONE_N_K
     */
    const MACHINE_ONE_N_K = 3;

    /**
     * @const MACHINE_ONE_N_K
     */
    const MACHINE_ONE_N_P_K = 4;

    /**
     * @const MACHINE_TWO_DYNAMIC_MAIN
     */
    const MACHINE_TWO_DYNAMIC_MAIN = 5;

    /**
     * @const MACHINE_TWO_DYNAMIC_SUB
     */
    const MACHINE_TWO_DYNAMIC_SUB = 6;

    /**
     * @const MACHINE_TWO_SUB_SUPPORT_P
     */
    const MACHINE_TWO_SUB_SUPPORT_P = 7;

    /**
     * @const MACHINE_TWO_SUB_SUPPORT_K
     */
    const MACHINE_TWO_SUB_SUPPORT_K = 8;

    /**
     *
     * @var $_typeRequired
     */
    private $_typeRequired;

    /**
     *
     * @var $_machineTwoType
     */
    private $_machineTwoType;

    /**
     *
     * @var $_listNitrogen
     */
    private $_listNitrogen;

    /**
     *
     * @var $_listNitrogenGeo
     */
    private $_listNitrogenGeo;
    /**
     *
     * @var $_listNitrogenGeo
     */
    private $_listGeoAndCode;

    private $_isMultipleCodes;


    /**
     *
     * @var $_amounts
     */
    private $_amounts;

    /**
     * define the cursor name for postgresql function get_data_for_fertilizer_map_creation
     */
    const polygonSelectionArea = "polygonSelectionArea";
    const crop ="crop";
    const fertilizationStandard ="fertilizationStandard";
    const fertilizationDivision = "fertilizationDivision";
    const userFertilizerDefinitionDetails = "userFertilizerDefinitionDetails";
    const selectedNitrogens = "selectedNitrogens";
    const systemFertilizerDefinitionDetailNitos1 = "systemFertilizerDefinitionDetailNitos1";
    const systemFertilizerDefinitionDetailNitos2 = "systemFertilizerDefinitionDetailNitos2";
    const userFertilizerDefinitionDetailNitos = "userFertilizerDefinitionDetailNitos";
    const systemFertilizerDefinitionDetailKalis ="systemFertilizerDefinitionDetailKalis";
    const systemFertilizerDefinitionDetailPhotphos = "systemFertilizerDefinitionDetailPhotphos";
    const userFertilizerDefinitionDetailKalis ="userFertilizerDefinitionDetailKalis";
    const userFertilizerDefinitionDetailPhotphos ="userFertilizerDefinitionDetailPhotphos";
    /**
     * Define the variable to hold data that're retrieved from sql function get_data_for_fertilizer_map_creation
     */
    public $_geoPolygonSelectionArea;
    public $_crop;
    public $_fertilizationStandard;
    public $_fertilizationDivision;
    public $_userFertilizerDefinitionDetails;
    public $_selectedNitrogens;
    public $_systemFertilizerDefinitionDetailNitos1;
    public $_systemFertilizerDefinitionDetailNitos2;
    public $_userFertilizerDefinitionDetailNitos;
    public $_systemFertilizerDefinitionDetailKalis;
    public $_systemFertilizerDefinitionDetailPhotphos;
    public $_userFertilizerDefinitionDetailKalis;
    public $_userFertilizerDefinitionDetailPhotphos;
    /**
     * The constructor to initialize a new instance of MapService
     */
    function __construct()
    {
        $this->_typeRequired = array(
            self::TYPE_KALI,
            self::TYPE_NITO,
            self::TYPE_PHOTPHO
        );

        $this->_machineTwoType = array(
            self::MACHINE_TWO_TYPE_MAIN,
            self::MACHINE_TWO_TYPE_SUB
        );
    }

    /**
     * Confirm Map data & save all to database
     *
     * @param array() $postData
     *
     * @return Gis\Models\Entities\FertilizerMap
     */
    public function confirmMapData($postData)
    {
        $layer=null;
        $this->getDataToCreateFertilizerMap($postData);
        DB::transaction(function () use($postData, &$layer)
        {
            $fertilizerMap = $this->createOrLoadFertilizerMap($postData);
            if (! $postData['isCreate'])
                $this->clearFertilizerData($fertilizerMap);
            $layer = $fertilizerMap->folderLayer;
            $this->createFertilizerProperties($postData, $fertilizerMap->id);
            $this->createStages($postData, $fertilizerMap->id);
            $this->setNewMesData($postData['fertility_map_id'], $postData['mesh_size'],
                json_decode($postData['listNitrogens'], true));
            if ($this->_isMultipleCodes &&($this->_fertilizationStandard[0]->created_by==0)&&count($this->_fertilizationDivision)!=0)
            {
                $this->createMapInfosWhenMultipleCodes($postData, $fertilizerMap->id, $layer->id);
            }else{
                $this->createMapInfos($postData, $fertilizerMap->id, $layer->id);
            }
            $this->createFertilizerOrganicMatter($postData, $fertilizerMap->id);
            $this->createFertilizerMapSelection($postData, $fertilizerMap->id);
            MapTools::makeFertilizerFile($fertilizerMap->id,$postData['user_id_main']);
        });
        return $layer;
    }


    /**
     * Get overall necessary data to create fertilizer map
     * @param $postData
     */
    private function getDataToCreateFertilizerMap($postData){
        //we use sql function get_data_for_fertilizer_map_creation to get data from database
        //this function contains 13 refcursors
        $selectionArea = $this->getPolygonSelectionAreaAsText($postData);
        $cropId = $postData['crops_id'];
        $fertilizerStandardDefinitionId = $postData['fertilizer_standard_definition_id'];
        $listSelectedFerilityMapInfoIds = implode(",",json_decode($postData['listNitrogens']));
        $pkRatio = $this->getPkRatio($postData['soil_analysis_type'], $postData[self::TYPE_PHOTPHO],
            $postData[self::TYPE_KALI]);
        DB::beginTransaction();
        $queryTemplate = "select get_data_for_fertilizer_map_creation(%s,%s,%s,'{%s}',%s,%s,%s,
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s',
        '%s');";
        $rawQuery = sprintf($queryTemplate,$selectionArea,$cropId,$fertilizerStandardDefinitionId,
            $listSelectedFerilityMapInfoIds,$fertilizerStandardDefinitionId,$pkRatio[self::TYPE_KALI],
            $pkRatio[self::TYPE_PHOTPHO],
            self::polygonSelectionArea,
            self::crop,
            self::fertilizationStandard,
            self::fertilizationDivision,
            self::userFertilizerDefinitionDetails,
            self::selectedNitrogens,
            self::systemFertilizerDefinitionDetailNitos1,
            self::systemFertilizerDefinitionDetailNitos2,
            self::userFertilizerDefinitionDetailNitos,
            self::systemFertilizerDefinitionDetailKalis,
            self::systemFertilizerDefinitionDetailPhotphos,
            self::userFertilizerDefinitionDetailKalis,
            self::userFertilizerDefinitionDetailPhotphos);
        $resultSets = DB::select(DB::raw($rawQuery));
        foreach($resultSets as $result){
            switch ($result->get_data_for_fertilizer_map_creation){
                case self::polygonSelectionArea:
                    $this->_geoPolygonSelectionArea =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::crop:
                    $this->_crop =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::fertilizationStandard:
                    $this->_fertilizationStandard =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::fertilizationDivision:
                    $this->_fertilizationDivision =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::userFertilizerDefinitionDetails:
                    $this->_userFertilizerDefinitionDetails =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::selectedNitrogens:
                    $this->_selectedNitrogens =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::systemFertilizerDefinitionDetailNitos1:
                    $this->_systemFertilizerDefinitionDetailNitos1 =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::systemFertilizerDefinitionDetailNitos2:
                    $this->_systemFertilizerDefinitionDetailNitos2 =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::userFertilizerDefinitionDetailNitos:
                    $this->_userFertilizerDefinitionDetailNitos =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::systemFertilizerDefinitionDetailKalis:
                    $this->_systemFertilizerDefinitionDetailKalis =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::systemFertilizerDefinitionDetailPhotphos:
                    $this->_systemFertilizerDefinitionDetailPhotphos =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::userFertilizerDefinitionDetailKalis:
                    $this->_userFertilizerDefinitionDetailKalis =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
                case self::userFertilizerDefinitionDetailPhotphos:
                    $this->_userFertilizerDefinitionDetailPhotphos =
                        DB::select(DB::raw(sprintf("FETCH ALL IN \"%s\";",$result->get_data_for_fertilizer_map_creation)));
                    break;
            }
        }
        DB::commit();
    }

    /**
     * Clear fertilizer data with property,stage,organic matter,map infos
     *
     * @param Gis\Models\Entities\FertilizerMap $fertylizerMap
     *
     * @return boolean
     */
    private function clearFertilizerData(FertilizerMap $fertylizerMap)
    {
        $fertylizerMap->organicMatterField()->delete();
        $fertylizerMap->fertilizerStage()->delete();
        $fertylizerMap->fertilizerMapProperty()->delete();
        $fertylizerMap->fertilizerMapInfo()->delete();

        return true;
    }

    /**
     * Create map selections data.
     *
     * @param array() $postData
     * @param int $fertilizerMapId
     *
     * @return
     *
     */
    private function createFertilizerMapSelection($postData, $fertilizerMapId)
    {
        if (! $postData['isCreate']){
            FertilityMapSelectionFacade::selectModel()->where('fertility_map_id',$postData['fertility_map_id'])
                ->where('fertilizer_map_id',$fertilizerMapId)
                ->update(['crops_id' => $postData['crops_id']]);
            return true;
        }
        $insert = [
            'fertility_map_id' => $postData['fertility_map_id'],
            'crops_id' => $postData['crops_id'],
            'user_id' => $postData['user_id_main'],
            'is_ready' => false,
            'fertilizer_map_id' => $fertilizerMapId
        ];
        $insert = $this->modifyData($insert, true);
        $fertility_map_selection = FertilityMapSelectionFacade::create($insert);

        foreach (json_decode($postData['listNitrogens']) as $mapInfoId) {
            $insert = [
                'fertility_map_selection_id' => $fertility_map_selection->id,
                'map_info_id' => $mapInfoId
            ];
            $attributes[] = $this->modifyData($insert, true);
        }

        FertilityMapSelectionInfoFacade::createFertilityMapselectionInfo($attributes);
    }

    /**
     * Set data for new meshsize
     *
     * @param int $fertilityMapId
     * @param int $meshSize
     * @param array() $listNitroGen
     *
     */
    public function setNewMesData($fertilityMapId, $meshSize, $listNitroGen)
    {
        $newMesData = GeoTools::reCreateUserChosenWithMeshSize($fertilityMapId, $meshSize, $listNitroGen);
        $arrCodes = array();
        foreach ($newMesData as $index => $data) {
            $dataDecode = json_decode($data, true);
            if (empty($dataDecode['mix']))
                continue;
            $totalRatio = 0;
            $totalNito = 0;

            foreach ($dataDecode['mix'] as $item) {
                $itemArr = explode(",", $item);
                $totalRatio += $itemArr[0];
                $totalNito += $itemArr[0] * $itemArr[1];
            }
            $this->_listNitrogenGeo[$dataDecode['geo']] = round($totalNito / $totalRatio);
            $this->_listGeoAndCode[$dataDecode['geo']] = $dataDecode['code'];
            foreach($dataDecode['code'] as $code){
                $arrLoop=[0,0,0,0];
                switch(substr($code,3)){
                    case"1":{$arrLoop[2]=$code;break;}
                    case"2":{$arrLoop[3]=$code;break;}
                    case"3":{$arrLoop[0]=$code;break;}
                    case"4":{$arrLoop[1]=$code;break;}
                }
                array_push($arrCodes,$code);
            }
            foreach($arrLoop as $value){
                if($value>0) {$this->_listGeoAndCode[$dataDecode['geo']]=$value; break;}
            }
        }
        if(is_null($this->_listNitrogenGeo))
        {
            throw new GisException(trans('common.create_fertilizer_map_null_nitrogen'), SystemCode::NOT_FOUND);
        }
        if(count(array_unique(array_values($arrCodes)))>1){
            $this->_isMultipleCodes = true;
        }
        else{
            $this->_isMultipleCodes = false;
        }
        $this->_listNitrogen = array_unique(array_values($this->_listNitrogenGeo));
        sort($this->_listNitrogen);
    }

     /**
     * Create New or load fertilizer map
     *
     * @param array() $postData
     * @return Gis\Models\Entities\Fertilizer
     */
    private function createOrLoadFertilizerMap($postData)
    {
        if (count($this->_fertilizationStandard)==0)
            throw new GisException(trans('common.fertilizer_standard_definiton_not_found'), SystemCode::NOT_FOUND);
        if ($postData['isCreate']) {
            $dateTime = Carbon::now('Japan');
            $layerName = $dateTime . ' ' . $postData['crop_name'];
            $layer = FolderServiceFacade::createLayerMap(FolderService::FOLDER_TYPE_FERTILIZER,
                $postData['user_id_main'], $layerName);
            $insertData = array(
                'user_id' => $postData['user_id_main'],
                'is_paid' => false,
                'layer_id' => $layer->id,
                'fertility_map_id' => $postData['fertility_map_id'],
                'geo'=>$this->getPolygonSelectionArea($postData)
            );
            $insertData = $this->modifyData($insertData, true);
            $fertilizer = FertilizerFacade::createFertilizerMap($insertData);
            //when create new fertilizer map
            ApplicationLogFacade::logAction(LoggingAction::MODE2_SPECIFY_CONDITION_TO_CREATE_FERTILIZER_MAP,$postData);
        } else {
            $fertilizer = FertilizerMapFacade::findByField('id', $postData['fertilizer_map_id'])->first();
            if (empty($fertilizer))
                throw new GisException(trans('common.fertilizer_map_not_found'), SystemCode::NOT_FOUND);
            //when create new fertilizer map
            ApplicationLogFacade::logAction(LoggingAction::ACTION_CONFIRM_CONDITION_TO_EDIT_FERTILIZER_MAP,$postData);
        }

        return $fertilizer;
    }

    /**
     * Create Polygon selection area when create new a fertilizer map
     * @param $postData
     * @return mixed
     */
    private function getPolygonSelectionArea($postData){
        //is_recreate_fertilizer_map
        if(!empty($postData["is_recreate_fertilizer_map"]) &&
            $postData["is_recreate_fertilizer_map"] == "1")
        {
            return $postData['polygonSelectionAreaForFertilizerCreationConfirmation'];;
        }
        $polygonSelectionAreaForFertilizerCreationConfirmation =
            $postData['polygonSelectionAreaForFertilizerCreationConfirmation'];
        $polygonData = "";
        $count =0;
        $arrPolygon =explode(",",$polygonSelectionAreaForFertilizerCreationConfirmation);
        foreach($arrPolygon as $str){
            if(($count%2) ==1){
                if($count == count($arrPolygon)-1)
                    $polygonData=$polygonData." ".$str;
                else
                    $polygonData=$polygonData." ".$str.",";
            }
            else{
                $polygonData=$polygonData." ".$str;
            }
            $count++;
        }
        //create geommetry for selection area
        $geoData = GeoTools::extractGeoFromPolygon($polygonData);
        return $geoData[0]->st_geomfromtext;
    }

    /**
     * Get polygon selection area
     * @param $postData
     * @return string
     */
    private function getPolygonSelectionAreaAsText($postData){
        //is_recreate_fertilizer_map
        if(!empty($postData["is_recreate_fertilizer_map"]) &&
            $postData["is_recreate_fertilizer_map"] == "1")
        {
            return "'".$postData['polygonSelectionAreaForFertilizerCreationConfirmation']."'" ;
        }
        $polygonSelectionAreaForFertilizerCreationConfirmation =
            $postData['polygonSelectionAreaForFertilizerCreationConfirmation'];
        $polygonData = "";
        $count =0;
        $arrPolygon =explode(",",$polygonSelectionAreaForFertilizerCreationConfirmation);
        foreach($arrPolygon as $str){
            if(($count%2) ==1){
                if($count == count($arrPolygon)-1)
                    $polygonData=$polygonData." ".$str;
                else
                    $polygonData=$polygonData." ".$str.",";
            }
            else{
                $polygonData=$polygonData." ".$str;
            }
            $count++;
        }
        //create geommetry for selection area
        return sprintf("'POLYGON((%s))'",$polygonData);
    }

    /**
     * Init Properties of fertilizer map
     *
     * @param array() $postData
     * @param int $fertilizerId
     *
     * @return Gis\Models\Entities\FertilizerProperty $fertilizerProperty
     */
    private function createFertilizerProperties($postData, $fertilizerId)
    {
        $insertData = FertilizerFacade::filterPropertyData($postData);

        if (empty($insertData))
            throw new GisException(trans('common.invalid_param'), SystemCode::BAD_REQUEST);

        $insertData = $this->modifyData($insertData, true);
        $insertData['fertilizer_map_id'] = $fertilizerId;
        if ($insertData['soil_analysis_type'] == self::SOIL_ANALYSIS_DISABLE) {
            $insertData[self::TYPE_KALI] = self::SOIL_ANALYSIS_STANDARD_RATIO;
            $insertData[self::TYPE_PHOTPHO] = self::SOIL_ANALYSIS_STANDARD_RATIO;
        }

        $fertilizerProperty = FertilizerFacade::createFertilizerProperty($insertData);
        return $fertilizerProperty;
    }

    /**
     * Create fertilizer Organic matter
     *
     * @param array() $postData
     * @param int $fertilizerMapId
     *
     * @return boolean
     */
    private function createFertilizerOrganicMatter($postData, $fertilizerMapId)
    {
        $attributes = array();
        foreach (json_decode($postData['organic_matter_fields'], true) as $index => $organicMatter) {
            if ($index >= 4 || (trim($organicMatter[self::TYPE_NITO]) == '' and trim($organicMatter[self::TYPE_KALI]) == '' and trim($organicMatter[self::TYPE_PHOTPHO]) == ''))
                continue;
            $attributes[] = $this->modifyData(array(
                'organic_matter_field_type' => $organicMatter['type'],
                self::TYPE_NITO => $organicMatter[self::TYPE_NITO] ? $organicMatter[self::TYPE_NITO] : 0,
                self::TYPE_KALI => $organicMatter[self::TYPE_KALI] ? $organicMatter[self::TYPE_KALI] : 0,
                self::TYPE_PHOTPHO => $organicMatter[self::TYPE_PHOTPHO] ? $organicMatter[self::TYPE_PHOTPHO] : 0,
                'fertilizer_map_id' => $fertilizerMapId
            ), true);
        }

        return FertilizerFacade::createOrganicMatterFields($attributes);
    }

    /**
     * Generate array Map nitrogen with RGB index
     *
     * @param unknown $totalRequired
     * @param String $type
     *
     * @return array() $nitroRgbIndex
     */
    private function getNitrogenRgbIndex($totalRequired, $type)
    {
        $fertilizers = array();
        foreach ($totalRequired as $nitrogen => $item) {
            $fertilizers[$nitrogen] = $item[$type];
        }
        arsort($fertilizers);
        $uniqueAmount = array_unique(array_values($fertilizers));
        rsort($uniqueAmount);
        $nitroRgbIndex = array();
        foreach ($fertilizers as $nitrogen => $amount) {
            $nitroRgbIndex[$nitrogen] = array_search($amount, $uniqueAmount);
        }

        return $nitroRgbIndex;
    }
    /**
     * Generate array Map nitrogen with RGB index
     *
     * @param unknown $totalRequired
     * @param String $type
     *
     * @return array() $nitroRgbIndex
     */
    private function getNitrogenRgbIndexWhenMultipleCodes($arrayResults, $type)
    {
        $amounts = array();
        foreach($arrayResults as $fertilizerRequired) {
            foreach ($fertilizerRequired as $mesh) {
                array_push($amounts, $mesh[$type]);
            }
        }
        arsort($amounts);
        $uniqueAmount = array_values(array_unique($amounts));
        $fertilizers = array();
        foreach($arrayResults as $code=>$totalRequired) {
            foreach ($totalRequired as $nitrogen => $item) {
                $fertilizers[$code][$nitrogen] = $item[$type];
            }
        }
        $nitroRgbIndex = array();
        foreach($fertilizers as $code=>$fertilizer) {
            foreach ($fertilizer as $nitrogen => $amount) {
                $val= array_search($amount, $uniqueAmount);
                $nitroRgbIndex[$code][$nitrogen] = ($val<11)? $val:10;
            }
        }
        return $nitroRgbIndex;
    }

    /**
     * Calculate total fertilizer with each nitrogen & store to DB
     *
     * @param array() $postData
     * @param int $fertilizerMapId
     *
     * @return boolean
     */
    private function createMapInfos($postData, $fertilizerMapId)
    {
        $totalRequired = $this->getListTotalFertilizer($postData);
        $type = $postData['control_methodology'] == self::MACHINE_TWO_DYNAMIC_SUB ? self::MACHINE_TWO_TYPE_SUB : self::MACHINE_TWO_TYPE_MAIN;
        $standardardRgb = $this->getStandardRgb($type, $totalRequired);
        $nitrogenRgbIndex = $this->getNitrogenRgbIndex($totalRequired, $type);
        $attributes = array();
        foreach ($this->_listNitrogenGeo as $geo => $nitrogen) {
            $newRgb = $this->findNewMeshColor($standardardRgb, $nitrogen, $nitrogenRgbIndex);
            if($newRgb == null)
                throw new GisException(trans('common.fertilization_division_not_found'), SystemCode::NOT_FOUND);
            $attributes[] = $this->modifyData(array(
                'geo' => $geo,
                'fertilizer_id' => $fertilizerMapId,
                'main_fertilizer' =>$totalRequired[$nitrogen][self::MACHINE_TWO_TYPE_MAIN],
                'sub_fertilizer' =>$totalRequired[$nitrogen][self::MACHINE_TWO_TYPE_SUB],
                'r' => $newRgb ? $newRgb['r'] : 0,
                'g' => $newRgb ? $newRgb['g'] : 0,
                'b' => $newRgb ? $newRgb['b'] : 0
            ), true);
        }
        return FertilizerFacade::createFertilizerMapInfos($attributes);
    }

    /**
     * Calculate total fertilizer with each nitrogen & store to DB
     *
     * @param array() $postData
     * @param int $fertilizerMapId
     *
     * @return boolean
     */
    private function createMapInfosWhenMultipleCodes($postData, $fertilizerMapId)
    {
        $arrayResults = $this->getListTotalFertilizerWhenMultipleCodes($postData);
        $type = $postData['control_methodology'] == self::MACHINE_TWO_DYNAMIC_SUB ? self::MACHINE_TWO_TYPE_SUB : self::MACHINE_TWO_TYPE_MAIN;
        DB::transaction(function () use($arrayResults, $type,$fertilizerMapId) {
            $standardardRgb = $this->getStandardRgbWhenMultipleCodes($type, $arrayResults);
            $nitrogenRgbIndex = $this->getNitrogenRgbIndexWhenMultipleCodes($arrayResults, $type);
            $attributes = array();
            foreach ($this->_listNitrogenGeo as $geo => $nitrogen) {
                $code=$this->_listGeoAndCode[$geo];
                $newRgb = $this->findNewMeshColorWhenMultipleCodes($standardardRgb, $nitrogen, $nitrogenRgbIndex, $code);
                if ($newRgb == null)
                    throw new GisException(trans('common.fertilization_division_not_found'), SystemCode::NOT_FOUND);
                $attributes[] = $this->modifyData(array(
                    'geo' => $geo,
                    'fertilizer_id' => $fertilizerMapId,
                    'main_fertilizer' => $arrayResults[$code][$nitrogen][self::MACHINE_TWO_TYPE_MAIN],
                    'sub_fertilizer' => $arrayResults[$code][$nitrogen][self::MACHINE_TWO_TYPE_SUB],
                    'r' => $newRgb ? $newRgb['r'] : 0,
                    'g' => $newRgb ? $newRgb['g'] : 0,
                    'b' => $newRgb ? $newRgb['b'] : 0
                ), true);
            }
            FertilizerFacade::createFertilizerMapInfos($attributes);
        });
        return true;
    }

    /**
     * Get standard RGB By number pattern
     *
     * @param String $type
     * @param array() $totalRequired
     *
     * @return array() $rgb
     */
    private function getStandardRgb($type, $totalRequired)
    {
        $rgb = array();
        $numOfpattern = $this->countNumOfPattren($type, $totalRequired);
        $mapColors = MapColorFacade::findByNumbOfPettern($numOfpattern);
        foreach ($mapColors as $mapColor) {
            array_push($rgb, array(
                'r' => $mapColor->r,
                'g' => $mapColor->g,
                'b' => $mapColor->b
            ));
        }
        return array_reverse($rgb);
    }
    /**
     * Get standard RGB By number pattern
     *
     * @param String $type
     * @param array() $totalRequired
     *
     * @return array() $rgb
     */
    private function getStandardRgbWhenMultipleCodes($type, $totalRequired)
    {
        $rgb = array();
        $getNumOfpattern = $this->countNumOfPattrenWhenMultipleCodes($type, $totalRequired);
        $numOfpattern= ($getNumOfpattern < 11)? $getNumOfpattern :11;
        $mapColors = MapColorFacade::findByNumbOfPettern($numOfpattern);
        foreach ($mapColors as $mapColor) {
            array_push($rgb, array(
                'r' => $mapColor->r,
                'g' => $mapColor->g,
                'b' => $mapColor->b
            ));
        }
        return array_reverse($rgb);
    }

    /**
     * find number of patterns with new mes data
     *
     * @param String $type
     * @param array() $fertilizerRequired
     *
     * @return int $numbPattern
     */
    private function countNumOfPattren($type, $fertilizerRequired)
    {
        $amounts = array();
        foreach ($fertilizerRequired as $mesh) {
            array_push($amounts, $mesh[$type]);
        }
        return count(array_unique($amounts));
    }
    /**
     * find number of patterns with new mes data
     *
     * @param String $type
     * @param array() $fertilizerRequired
     *
     * @return int $numbPattern
     */
    private function countNumOfPattrenWhenMultipleCodes($type, $totalRequired)
    {
        $amounts = array();
        foreach($totalRequired as $fertilizerRequired) {
            foreach ($fertilizerRequired as $mesh) {
                array_push($amounts, $mesh[$type]);
            }
        }
        return count(array_unique($amounts));
    }

    /**
     * Find RGB for new mesh
     *
     * @param array() $standardRgb
     * @param int $nitrogen
     * @param array() $nitroRgbIndex
     *
     * @return array() $rgb
     */
    private function findNewMeshColor($standardRgb, $nitrogen, $nitroRgbIndex)
    {
        if(!isset($nitroRgbIndex[$nitrogen])) return null;
        $rgbIndex = $nitroRgbIndex[$nitrogen];
        return $standardRgb[$rgbIndex];
    }
    /**
     * Find RGB for new mesh
     *
     * @param array() $standardRgb
     * @param int $nitrogen
     * @param array() $nitroRgbIndex
     *
     * @return array() $rgb
     */
    private function findNewMeshColorWhenMultipleCodes($standardRgb, $nitrogen, $nitroRgbIndex ,$code)
    {
        if(!isset($nitroRgbIndex[$code][$nitrogen])) return null;
        $rgbIndex = $nitroRgbIndex[$code][$nitrogen];
        return $standardRgb[$rgbIndex];
    }

    /**
     * Create fertilizer stages
     *
     * @param array() $postData
     * @param int $fertilizerMapId
     *
     * @return boolean
     */
    private function createStages($postData, $fertilizerMapId)
    {
        $stages = json_decode($postData['fertilization_stages'], true);

        $attributes = array();
        foreach ($stages as $index => $stage) {
            if (trim($stage['fertilization_stage']) == '')
                continue;
            $attributes[$index]['fertilizer_map_id'] = $fertilizerMapId;
            $attributes[$index]['fertilization_stage'] = $stage['fertilization_stage'];
            $attributes[$index][self::TYPE_NITO] = $stage[self::TYPE_NITO] ? $stage[self::TYPE_NITO] : 0;
            $attributes[$index][self::TYPE_KALI] = $stage[self::TYPE_KALI] ? $stage[self::TYPE_KALI] : 0;
            $attributes[$index][self::TYPE_PHOTPHO] = $stage[self::TYPE_PHOTPHO] ? $stage[self::TYPE_PHOTPHO] : 0;
            $attributes[$index] = $this->modifyData($attributes[$index], true);
        }
        return FertilizerFacade::createStages($attributes);
    }

    /**
     * Get list total of fertilizer required
     *
     * @param array() $postData
     *
     * @return array() $totalRequired
     */
    private function getListTotalFertilizer($postData)
    {
        $totalRequired = array();
        $crop = $this->_crop[0];
        if (empty($crop))
            throw new GisException(trans('common.confirm_map_crop_undefined'), SystemCode::NOT_FOUND);
        $fertilizerStard = $this->_fertilizationStandard[0];
        $npkExists = $this->getNpkExists($postData['organic_matter_fields'], $postData['fertilization_stages']);
        $isSystemSystemStandardFertilizer = false;
        $requiredId['crop_id'] = $postData['crops_id'];
        $requiredId['fertilizer_standard_id'] = $postData['fertilizer_standard_definition_id'];
        $fertilizationDivisions= array([0]);
        if(count($this->_fertilizationDivision)>0)
            $fertilizationDivisions =$this->_fertilizationDivision;
        $standardCrop = null;
        if ($fertilizerStard->created_by == self::FERTILIZER_STANDARD_SYSTEM) {
            //get from system fertilization firstly
            //-- if cannot find data from system fertilization
            //-- we continue to get data from fertilization division
            $isSystemSystemStandardFertilizer = true;
        } else {
            $standardCrop = $this->_fertilizationStandard[0];
            if (empty($standardCrop) || count($standardCrop) == 0)
                throw new GisException(trans('common.standard_crop_not_found'), SystemCode::NOT_FOUND);
            $requiredId['user_defined_id'] = $standardCrop->id;
        }

        $pkRatio = $this->getPkRatio($postData['soil_analysis_type'], $postData[self::TYPE_PHOTPHO], $postData[self::TYPE_KALI]);
        foreach($fertilizationDivisions as $fertilizationDivision){
            $standardNitos = $this->getStandardNito($fertilizationDivision, $standardCrop,
                $isSystemSystemStandardFertilizer,
                $fertilizerStard->fertilization_standard_name);
            $nitoRequired = $this->getNitoRequired($standardNitos, $npkExists[self::TYPE_NITO], $crop);

            $standardPk = $this->getStandardPk($npkExists, $isSystemSystemStandardFertilizer, $pkRatio,
                $requiredId, $fertilizationDivision);
            $percentNpk = $this->getPercentNpkOfMachine($postData['fertilizing_machine_type'], $postData);
            $typeNeedCheck = $this->getTypeNeedCheck($postData['control_methodology']);
            if ($postData['fertilizing_machine_type'] == self::FERTILIZER_MACHINE_ONE) {
                foreach ($nitoRequired as $nitrogen => $nitoAmount) {
                    $totalRequired[$nitrogen] = $this->calculateTotalFertilizerMachineOne($nitoAmount, $percentNpk, $standardPk, $typeNeedCheck);
                }
            } else {
                foreach ($nitoRequired as $nitrogen => $totalNito) {
                    $totalRequired[$nitrogen] = $this->calculateTotalFertilizerMachineTwo($totalNito, $percentNpk, $postData['control_methodology'], $postData['fixed_fertilizer_amount'], $standardPk, $typeNeedCheck);
                }
            }
        }
        return $this->recalculateWithMeshSize($postData['mesh_size'], $totalRequired);
    }

    /**
     * Get list total of fertilizer required
     *
     * @param array() $postData
     *
     * @return array() $totalRequired
     */
    private function getListTotalFertilizerWhenMultipleCodes($postData)
    {
        $totalRequired = array();
        $crop = $this->_crop[0];
        if (empty($crop))
            throw new GisException(trans('common.confirm_map_crop_undefined'), SystemCode::NOT_FOUND);
        $fertilizerStard = $this->_fertilizationStandard[0];
        $npkExists = $this->getNpkExists($postData['organic_matter_fields'], $postData['fertilization_stages']);
        $isSystemSystemStandardFertilizer = false;
        $requiredId['crop_id'] = $postData['crops_id'];
        $requiredId['fertilizer_standard_id'] = $postData['fertilizer_standard_definition_id'];
        $fertilizationDivisions= array([0]);
        if(count($this->_fertilizationDivision)>0)
            $fertilizationDivisions =$this->_fertilizationDivision;
        $standardCrop = null;
        if ($fertilizerStard->created_by == self::FERTILIZER_STANDARD_SYSTEM) {
            //get from system fertilization firstly
            //-- if cannot find data from system fertilization
            //-- we continue to get data from fertilization division
            $isSystemSystemStandardFertilizer = true;
        } else {
            $standardCrop = $this->_fertilizationStandard[0];
            if (empty($standardCrop) || count($standardCrop) == 0)
                throw new GisException(trans('common.standard_crop_not_found'), SystemCode::NOT_FOUND);
            $requiredId['user_defined_id'] = $standardCrop->id;
        }

        $pkRatio = $this->getPkRatio($postData['soil_analysis_type'], $postData[self::TYPE_PHOTPHO], $postData[self::TYPE_KALI]);
        $arrayResults=array();
        foreach($fertilizationDivisions as $fertilizationDivision){
            $standardNitos = $this->getStandardNitoWhenMultipleCodes($fertilizationDivision, $standardCrop,
                $isSystemSystemStandardFertilizer,
                $fertilizerStard->fertilization_standard_name);
            if(is_null($standardNitos))
                continue;
            $nitoRequired = $this->getNitoRequiredWhenMultipleCodes($standardNitos, $npkExists[self::TYPE_NITO], $crop);

            $standardPk = $this->getStandardPkWhenMultipleCodes($npkExists, $isSystemSystemStandardFertilizer, $pkRatio,
                $requiredId, $fertilizationDivision);
            $percentNpk = $this->getPercentNpkOfMachine($postData['fertilizing_machine_type'], $postData);
            $typeNeedCheck = $this->getTypeNeedCheck($postData['control_methodology']);
            if ($postData['fertilizing_machine_type'] == self::FERTILIZER_MACHINE_ONE) {
                foreach ($nitoRequired as $nitrogen => $nitoAmount) {
                    //add index as fertilization classification code
                    $totalRequired[$nitrogen] = $this->calculateTotalFertilizerMachineOne($nitoAmount, $percentNpk, $standardPk, $typeNeedCheck);
                }
            } else {
                foreach ($nitoRequired as $nitrogen => $totalNito) {
                    //add index as fertilization classification code
                    $totalRequired[$nitrogen] = $this->calculateTotalFertilizerMachineTwo($totalNito, $percentNpk, $postData['control_methodology'], $postData['fixed_fertilizer_amount'], $standardPk, $typeNeedCheck);
                }
            }
            $arrayResults[$fertilizationDivision->fertilization_classification_code]=$totalRequired;
        }
        return $this->recalculateWithMeshSizeWhenMultipleCodes($postData['mesh_size'], $arrayResults);
    }

    /**
     * Get PK ratio with soi analysis type
     *
     * @param int $soilAnalysisType
     * @param String $soilAnalysisP
     * @param String $soilAnalysisK
     *
     * @return array() $pkRatio
     */
    public function getPkRatio($soilAnalysisType, $soilAnalysisP, $soilAnalysisK)
    {
        $pkRatio = array(
            self::TYPE_KALI => self::SOIL_ANALYSIS_STANDARD_RATIO,
            self::TYPE_PHOTPHO => self::SOIL_ANALYSIS_STANDARD_RATIO
        );

        if ($soilAnalysisType == self::SOIL_ANALYSIS_ENABLE) {
            $pkRatio = array(
                self::TYPE_KALI => $soilAnalysisK,
                self::TYPE_PHOTPHO => $soilAnalysisP
            );
        }

        return $pkRatio;
    }

    /**
     * Recalculate total fertilizer each nitrogen with new mesh size
     *
     * @param int $meshSize
     * @param array $totalRequired
     *
     * @return array() $totalRequired
     */
    private function recalculateWithMeshSize($meshSize, $totalRequired)
    {
        foreach ($totalRequired as $index => $fertilizer) {
            $totalRequired[$index][self::MACHINE_TWO_TYPE_MAIN] = round($fertilizer[self::MACHINE_TWO_TYPE_MAIN],1);// * $ratioResize
            $totalRequired[$index][self::MACHINE_TWO_TYPE_SUB] = round($fertilizer[self::MACHINE_TWO_TYPE_SUB],1);// * $ratioResize
        }
        return $totalRequired;
    }
    /**
     * Recalculate total fertilizer each nitrogen with new mesh size
     *
     * @param int $meshSize
     * @param array $totalRequired
     *
     * @return array() $totalRequired
     */
    private function recalculateWithMeshSizeWhenMultipleCodes($meshSize, $arrayResults)
    {
        foreach($arrayResults as &$totalRequired) {
            foreach ($totalRequired as $index => $fertilizer) {
                $totalRequired[$index][self::MACHINE_TWO_TYPE_MAIN] = round($fertilizer[self::MACHINE_TWO_TYPE_MAIN], 1);// * $ratioResize
                $totalRequired[$index][self::MACHINE_TWO_TYPE_SUB] = round($fertilizer[self::MACHINE_TWO_TYPE_SUB], 1);// * $ratioResize
            }
        }
        return $arrayResults;
    }

    /**
     * Get Total Npk Exists from organic matter & stages
     *
     * @param array() $organicMatter
     * @param array() $stages
     *
     * @return array() $totalOrganicMatter
     */
    private function getNpkExists($organicMatter, $stages)
    {
        $organicMatter = json_decode($organicMatter, true);
        $stages = json_decode($stages, true);

        $totalOrganicMatter = array();
        foreach ($organicMatter as $item) {
            if ($item['type'] == self::ORGANIC_MATTER_TOTAL) {
                $totalOrganicMatter = $item;
                break;
            }
        }
        $totalStage = array(
            self::TYPE_KALI => 0,
            self::TYPE_NITO => 0,
            self::TYPE_PHOTPHO => 0
        );
        foreach ($stages as $index => $item) {
            if (empty($item['fertilization_stage']))
                continue;
            $totalStage[self::TYPE_KALI] += $item[self::TYPE_KALI];
            $totalStage[self::TYPE_NITO] += $item[self::TYPE_NITO];
            $totalStage[self::TYPE_PHOTPHO] += $item[self::TYPE_PHOTPHO];
        }

        return array(
            self::TYPE_KALI => $totalStage[self::TYPE_KALI] + $totalOrganicMatter[self::TYPE_KALI],
            self::TYPE_NITO => $totalStage[self::TYPE_NITO] + $totalOrganicMatter[self::TYPE_NITO],
            self::TYPE_PHOTPHO => $totalStage[self::TYPE_PHOTPHO] + $totalOrganicMatter[self::TYPE_PHOTPHO]
        );
    }

    /**
     * Get Percent N-P-K with each machine type
     *
     * @param int $machineType
     * @param array() $postData
     *
     * @return array() $percentNpk
     */
    private function getPercentNpkOfMachine($machineType, $postData)
    {
        return $machineType == self::FERTILIZER_MACHINE_ONE ? array(
            self::TYPE_NITO => $postData['one_barrel_n'],
            self::TYPE_KALI => $postData['one_barrel_k'],
            self::TYPE_PHOTPHO => $postData['one_barrel_p']
        ) : array(
            self::MACHINE_TWO_TYPE_MAIN => array(
                self::TYPE_NITO => $postData['main_fertilizer_n'],
                self::TYPE_KALI => $postData['main_fertilizer_k'],
                self::TYPE_PHOTPHO => $postData['main_fertilizer_p']
            ),
            self::MACHINE_TWO_TYPE_SUB => array(
                self::TYPE_NITO => $postData['sub_fertilizer_n'],
                self::TYPE_KALI => $postData['sub_fertilizer_k'],
                self::TYPE_PHOTPHO => $postData['sub_fertilizer_p']
            )
        );
    }

    /**
     * Calculate total fertilizer required for map with logic
     * With logic of machine one
     *
     * @param int $nitoAmount
     * @param array() $percentNpk
     * @param aray() $standardPk
     * @param array() $typeNeedCheck
     *
     * @return int $totalFertilizer
     */
    private function calculateTotalFertilizerMachineOne($nitoAmount, $percentNpk, $standardPk, $typeNeedCheck)
    {
        $totalFertilizer[self::MACHINE_TWO_TYPE_MAIN] = $nitoAmount * 100 / $percentNpk[self::TYPE_NITO];
        foreach ($this->_typeRequired as $type) {
            if ($type == self::TYPE_NITO)
                $result[$type] = $nitoAmount;
            else
                $result[$type] = $totalFertilizer[self::MACHINE_TWO_TYPE_MAIN] * $percentNpk[$type] / 100;
        }

        if (! empty($typeNeedCheck)) {
            foreach ($typeNeedCheck as $type) {
                if ($standardPk[$type] > $result[$type]) {
                    $totalFertilizer[self::MACHINE_TWO_TYPE_MAIN] = $standardPk[$type] * 100 / $percentNpk[$type];
                    foreach ($this->_typeRequired as $type) {
                        $result[$type] = $totalFertilizer[self::MACHINE_TWO_TYPE_MAIN] * $percentNpk[$type] / 100;
                    }
                }
            }
        }
        $totalFertilizer[self::MACHINE_TWO_TYPE_SUB] = 0;
        return $totalFertilizer;
    }

    /**
     * Calculate total fertilizer required for map with logic
     * With logic of machine two
     *
     * @param int $totalNito
     * @param array() $percentNpk
     * @param int $controlMethodology
     * @param int $fixedFertilizer
     * @param array() $standardPk
     * @param array() $typeNeedCheck
     *
     * @return int $totalFertilizer
     */
    private function calculateTotalFertilizerMachineTwo($totalNito, $percentNpk, $controlMethodology, $fixedFertilizer = 0, $standardPk, $typeNeedCheck)
    {
        $machineTypes = $this->_machineTwoType;
        if ($controlMethodology == self::MACHINE_TWO_DYNAMIC_MAIN || $controlMethodology == self::MACHINE_TWO_DYNAMIC_SUB) {
            $machineType = $controlMethodology == self::MACHINE_TWO_DYNAMIC_MAIN ? self::MACHINE_TWO_TYPE_SUB : self::MACHINE_TWO_TYPE_MAIN;
            foreach ($this->_typeRequired as $type) {
                $result[$machineType][$type] = ($fixedFertilizer * $percentNpk[$machineType][$type]) / 100;
            }

            unset($machineTypes[array_search($machineType, $machineTypes)]);

            $machineRestType = array_pop($machineTypes);
            $result[$machineRestType][self::TYPE_NITO] = $totalNito > $result[$machineType][self::TYPE_NITO] ? $totalNito - $result[$machineType][self::TYPE_NITO] : 0;
            $totalRest = ($result[$machineRestType][self::TYPE_NITO] * 100 )/ $percentNpk[$machineRestType][self::TYPE_NITO];
            return array(
                $machineRestType => $totalRest,
                $machineType => $fixedFertilizer
            );
        } else {
            $totalMain = ($totalNito * 100) / $percentNpk[self::MACHINE_TWO_TYPE_MAIN][self::TYPE_NITO];
            foreach ($this->_typeRequired as $type) {
                $result[self::MACHINE_TWO_TYPE_MAIN][$type] = ($totalMain * $percentNpk[self::MACHINE_TWO_TYPE_MAIN][$type] ) / 100;
            }
            $totalSub = 0;
            if (! empty($typeNeedCheck)) {
                $supportPkType = end($typeNeedCheck);
                foreach ($typeNeedCheck as $type) {
                    if ($standardPk[$supportPkType] > $result[self::MACHINE_TWO_TYPE_MAIN][$supportPkType]) {
                        $result[self::MACHINE_TWO_TYPE_SUB][$supportPkType] = $standardPk[$supportPkType] - $result[self::MACHINE_TWO_TYPE_MAIN][$supportPkType];
                        $totalSub = ($result[self::MACHINE_TWO_TYPE_SUB][$supportPkType] * 100 )/ $percentNpk[self::MACHINE_TWO_TYPE_SUB][$type];
                        foreach ($this->_typeRequired as $type) {
                            $result[self::MACHINE_TWO_TYPE_SUB][$type] = ($totalSub * $percentNpk[self::MACHINE_TWO_TYPE_SUB][$type] ) / 100;
                        }
                    }
                }
            }

            return array(
                self::MACHINE_TWO_TYPE_SUB => $totalSub,
                self::MACHINE_TWO_TYPE_MAIN => $totalMain
            );
        }
    }

    /**
     * Get Array type need check with control methodology
     *
     * @param int $controlMethodology
     *
     * @return array() $typeNeedCheck
     */
    private function getTypeNeedCheck($controlMethodology)
    {
        switch ($controlMethodology) {
            case self::MACHINE_ONE_N_P_K:
                return array(
                    self::TYPE_KALI,
                    self::TYPE_PHOTPHO
                );
                break;
            case self::MACHINE_ONE_N_P:
                return array(
                    self::TYPE_PHOTPHO
                );
                break;
            case self::MACHINE_ONE_N_K:
                return array(
                    self::TYPE_KALI
                );
                break;
            case self::MACHINE_TWO_SUB_SUPPORT_P:
                return array(
                    self::TYPE_PHOTPHO
                );
            case self::MACHINE_TWO_SUB_SUPPORT_K:
                return array(
                    self::TYPE_KALI
                );
            default:
                return array();
        }
    }

    /**
     * Get List Nito Standard
     *
     * @param Gis\Models\Entities\StandardCrop $fertilizerStandardId
     * @param Gis\Models\Entities\FertilizationDivision $fertilizationDivision
     * @param array() $requiredId
     *
     * @return array() $standardNito
     */
    private function getStandardNito($fertilizationDivision = null, $standardCrop = null,
                                     $isSystemSystemStandardFertilizer,$fertilizerStandardName)
    {
        $standardNitos = array();
        //get list of nitrogen in fertility map
        $listNitrogenOfFertilityMap = $this->_selectedNitrogens;
        if($isSystemSystemStandardFertilizer){
            $nitoStandard=$this->_systemFertilizerDefinitionDetailNitos1;
            if (count($nitoStandard)==0) {
                //get nito from fertilization division
                if(gettype($fertilizationDivision)!="object") {
                    if ((array)$fertilizationDivision[0]) {
                        throw new GisException(trans('common.fertilization_division_not_found'), SystemCode::NOT_FOUND);
                    }
                }
                $amount = self::PREFIX_DIVISION_AMOUNT . $fertilizationDivision->n;
                $nitoStandard = $this->_systemFertilizerDefinitionDetailNitos2;
                if (empty($nitoStandard) || is_null($nitoStandard) || count($nitoStandard)==0){

                    throw new GisException(
                        sprintf(trans('common.fertilizer_map_cannot_find_nitrogen_from_system'),
                            $fertilizerStandardName,implode(',',array_values($listNitrogenOfFertilityMap))),
                        SystemCode::NOT_FOUND);
                }
                $listMissingNitroGens = array();
                foreach ($nitoStandard as $nitoItem) {
                    //the processing will stop whenever division_amount will be null or empty
                    if(
                        (is_null($nitoItem->$amount) || empty($nitoItem->$amount))
                        && $this->in_array_r($nitoItem->n,$listNitrogenOfFertilityMap)
                    ){
                        array_push($listMissingNitroGens, $nitoItem->n);
                    }
                    $standardNitos[$nitoItem->n] = $nitoItem->$amount;
                }
                if(count($listMissingNitroGens)>0){
                    throw new GisException(
                        sprintf(trans('common.fertilizer_map_cannot_find_nitrogen_from_system'),
                            $fertilizerStandardName,implode(',',array_values($listMissingNitroGens))),
                        SystemCode::NOT_FOUND);
                }
            }
            else{
                foreach ($nitoStandard as $nitoItem) {
                    $standardNitos[$nitoItem->n] = $nitoItem->n_amount;
                }
            }
        }
        else{
            //get Nitrogen from user-defined fertilization firstly
            //if return null data then continue to get from fertilization division
            if(count($this->_userFertilizerDefinitionDetails)==0){
                throw new GisException(trans('common.fertilizer_not_avalable'),SystemCode::NOT_FOUND);
            }
            $nitoStandard=$this->_userFertilizerDefinitionDetailNitos;
            $listMissingNitroGens = array();
            foreach ($nitoStandard as $nitoItem) {
                //the processing will stop whenever division_amount will be null or empty
                if(
                    (is_null($nitoItem->fertilization_standard_amount)
                        || empty($nitoItem->fertilization_standard_amount)
                        || $nitoItem->fertilization_standard_amount ==0
                    )
                    && $this->in_array_r($nitoItem->nitrogen,$listNitrogenOfFertilityMap)
                ){
                    array_push($listMissingNitroGens, $nitoItem->nitrogen);
                }
                $standardNitos[$nitoItem->nitrogen] = $nitoItem->fertilization_standard_amount;
            }
            if(count($listMissingNitroGens)>0){
                asort($listMissingNitroGens);
                throw new GisException(
                    sprintf(trans('common.fertilizer_map_cannot_find_nitrogen_from_system'),
                        $fertilizerStandardName,implode(',',array_values($listMissingNitroGens))),
                    SystemCode::NOT_FOUND);
            }
        }
        return $standardNitos;
    }

    /**
     * Get List Nito Standard
     *
     * @param Gis\Models\Entities\StandardCrop $fertilizerStandardId
     * @param Gis\Models\Entities\FertilizationDivision $fertilizationDivision
     * @param array() $requiredId
     *
     * @return array() $standardNito
     */
    private function getStandardNitoWhenMultipleCodes($fertilizationDivision = null, $standardCrop = null,
                                     $isSystemSystemStandardFertilizer,$fertilizerStandardName)
    {
        $standardNitos = array();
        //get list of nitrogen in fertility map
        $listNitrogenOfFertilityMap = $this->_selectedNitrogens;
        if($isSystemSystemStandardFertilizer){
            $nitoStandard=$this->_systemFertilizerDefinitionDetailNitos1;
            if (count($nitoStandard)==0) {

                //get nito from fertilization division
                if(gettype($fertilizationDivision)!="object") {
                    if ((array)$fertilizationDivision[0]) {
                        throw new GisException(trans('common.fertilization_division_not_found'), SystemCode::NOT_FOUND);
                    }
                }
                //do not process with fertilization division
                //with fertilization classification code doesn't belong to $listCode
                $listCode=array_unique($this->_listGeoAndCode);
                if(!array_search($fertilizationDivision->fertilization_classification_code,$listCode)){
                    return null;
                }
                $amount = self::PREFIX_DIVISION_AMOUNT . $fertilizationDivision->n;
                $nitoStandard = $this->_systemFertilizerDefinitionDetailNitos2;
                if (empty($nitoStandard) || is_null($nitoStandard) || count($nitoStandard)==0){

                    throw new GisException(
                        sprintf(trans('common.fertilizer_map_cannot_find_nitrogen_from_system'),
                            $fertilizerStandardName,implode(',',array_values($listNitrogenOfFertilityMap))),
                        SystemCode::NOT_FOUND);
                }
                $listMissingNitroGens = array();
                foreach ($nitoStandard as $nitoItem) {
                    //the processing will stop whenever division_amount will be null or empty
                    if(
                        (is_null($nitoItem->$amount) || empty($nitoItem->$amount))
                        && $this->in_array_r($nitoItem->n,$listNitrogenOfFertilityMap)
                    ){
                        array_push($listMissingNitroGens, $nitoItem->n);
                    }
                    $standardNitos[$nitoItem->n] = $nitoItem->$amount;
                }
                if(count($listMissingNitroGens)>0){
                    throw new GisException(
                        sprintf(trans('common.fertilizer_map_cannot_find_nitrogen_from_system'),
                            $fertilizerStandardName,implode(',',array_values($listMissingNitroGens))),
                        SystemCode::NOT_FOUND);
                }
            }
            else{
                foreach ($nitoStandard as $nitoItem) {
                    $standardNitos[$nitoItem->n] = $nitoItem->n_amount;
                }
            }
        }
        else{
            //get Nitrogen from user-defined fertilization firstly
            //if return null data then continue to get from fertilization division
            $nitoStandard=$this->_userFertilizerDefinitionDetailNitos;
            $listMissingNitroGens = array();
            foreach ($nitoStandard as $nitoItem) {
                //the processing will stop whenever division_amount will be null or empty
                if(
                    (
                        is_null($nitoItem->fertilization_standard_amount)
                        || empty($nitoItem->fertilization_standard_amount)
                        || $nitoItem->fertilization_standard_amount ==0
                    )
                    && $this->in_array_r($nitoItem->nitrogen,$listNitrogenOfFertilityMap)
                ){
                    array_push($listMissingNitroGens, $nitoItem->nitrogen);
                }
                $standardNitos[$nitoItem->nitrogen] = $nitoItem->fertilization_standard_amount;
            }
            if(count($listMissingNitroGens)>0){
                asort($listMissingNitroGens);
                throw new GisException(
                    sprintf(trans('common.fertilizer_map_cannot_find_nitrogen_from_system'),
                        $fertilizerStandardName,implode(',',array_values($listMissingNitroGens))),
                    SystemCode::NOT_FOUND);
            }
        }
        //Add $standardNitos to array with index = fertilization classification code
        return $standardNitos;
    }
    private function in_array_r($item , $array){
        return preg_match('/"'.$item.'"/i' , json_encode($array));
    }
    /**
     * Get List Nito required after filter list nito standard by
     * standard Nito Of crop & fertility map nitos & stage nito exists
     *
     * @param array() $standardNitos
     * @param float $nitoExists
     * @param Gis\Models\Entities\Crop $crop
     *
     * @return array() $standardNitos
     */
    private function getNitoRequired($standardNitos, $nitoExists, $crop)
    {
        foreach ($standardNitos as $nitrogen => $nitoAmount) {
            $standardNitos[$nitrogen] = $nitoAmount - $nitoExists;
        }

        $standardNitos = $this->filterNitoByCropNito($crop, $standardNitos);
        return $standardNitos;
    }
    /**
     * Get List Nito required after filter list nito standard by
     * standard Nito Of crop & fertility map nitos & stage nito exists
     *
     * @param array() $standardNitos
     * @param float $nitoExists
     * @param Gis\Models\Entities\Crop $crop
     *
     * @return array() $standardNitos
     */
    private function getNitoRequiredWhenMultipleCodes($standardNitos, $nitoExists, $crop)
    {
        foreach ($standardNitos as $nitrogen => $nitoAmount) {
            $standardNitos[$nitrogen] = $nitoAmount - $nitoExists;
        }

        $standardNitos = $this->filterNitoByCropNito($crop, $standardNitos);
        return $standardNitos;
    }

    /**
     * Filter Standard nito with standard Nito Of crop
     *
     * @param Gis\Models\Entities\Crop $crop
     * @param array() $standardNitos
     *
     * @return array() $standardNitos
     */
    private function filterNitoByCropNito($crop, $standardNitos)
    {
        foreach ($standardNitos as $nitrogen => $nitoAmount) {
            if ($crop->n > $nitoAmount) {
                $standardNitos[$nitrogen] = $crop->n;
            }
        }

        return $standardNitos;
    }

    /**
     * Get P-K standard for map & crop & fertilizer standard
     *
     * @param array() $npkExists
     * @param boolean $isSystem
     * @param array() $pkRatio
     * @param array() $requiredId
     * @param Gis\Models\Entities\FertilizationDivision $fertilizationDivision
     *
     * @return array() $pkStandard
     */
    private function getStandardPk($npkExists, $isSystem, $pkRatio, $requiredId, $fertilizationDivision = null)
    {
        $standardP = 0;
        $standardK = 0;

        if ($isSystem) {
            $KData =(count($this->_systemFertilizerDefinitionDetailKalis)==0)? null: $this->_systemFertilizerDefinitionDetailKalis[0];
            $PData =(count($this->_systemFertilizerDefinitionDetailPhotphos)==0)? null: $this->_systemFertilizerDefinitionDetailPhotphos[0];
            if (empty($KData) || empty($KData->fertilization_standard_amount)) {
                if(gettype($fertilizationDivision)!="object") {
                    if ((array)$fertilizationDivision[0]) {
                        throw new GisException(trans('common.fertilization_division_not_found'), SystemCode::NOT_FOUND);
                    }
                }
                $standardK=$fertilizationDivision->k * $pkRatio[self::TYPE_KALI];
            }
            else {
                $standardK=$KData->fertilization_standard_amount;
            };
            if (empty($PData) || empty($PData->fertilization_standard_amount)) {
                if(gettype($fertilizationDivision)!="object") {
                    if ((array)$fertilizationDivision[0]) {
                        throw new GisException(trans('common.fertilization_division_not_found'), SystemCode::NOT_FOUND);
                    }
                }
                $standardP=$fertilizationDivision->p * $pkRatio[self::TYPE_KALI];
            }
            else {
                $standardP=$PData->fertilization_standard_amount;
            };
        } else {
            $KData = $this->_userFertilizerDefinitionDetailKalis;
            $PData = $this->_userFertilizerDefinitionDetailPhotphos;
            if(count($KData)==0 || count($PData)==0 || is_null($KData) || is_null($PData))
            {
                throw new GisException(trans('common.creation_fertilizer_map_not_found_p_k'), SystemCode::NOT_FOUND);
            }
            $standardK = $KData[0]->fertilization_standard_amount;
            $standardP = $PData[0]->fertilization_standard_amount;
        }

        return array(
            self::TYPE_KALI => $standardK > $npkExists[self::TYPE_KALI] ? $standardK - $npkExists[self::TYPE_KALI] : 0,
            self::TYPE_PHOTPHO => $standardP > $npkExists[self::TYPE_PHOTPHO] ? $standardP - $npkExists[self::TYPE_PHOTPHO] : 0
        );
    }

    /**
     * Get P-K standard for map & crop & fertilizer standard
     *
     * @param array() $npkExists
     * @param boolean $isSystem
     * @param array() $pkRatio
     * @param array() $requiredId
     * @param Gis\Models\Entities\FertilizationDivision $fertilizationDivision
     *
     * @return array() $pkStandard
     */
    private function getStandardPkWhenMultipleCodes($npkExists, $isSystem, $pkRatio, $requiredId, $fertilizationDivision = null)
    {
        $standardP = 0;
        $standardK = 0;

        if ($isSystem) {
            $KData =(count($this->_systemFertilizerDefinitionDetailKalis)==0)? null: $this->_systemFertilizerDefinitionDetailKalis[0];
            $PData =(count($this->_systemFertilizerDefinitionDetailPhotphos)==0)? null: $this->_systemFertilizerDefinitionDetailPhotphos[0];
            if (empty($KData) || empty($KData->fertilization_standard_amount)) {
                if(gettype($fertilizationDivision)!="object") {
                    if ((array)$fertilizationDivision[0]) {
                        throw new GisException(trans('common.fertilization_division_not_found'), SystemCode::NOT_FOUND);
                    }
                }
                $standardK=$fertilizationDivision->k * $pkRatio[self::TYPE_KALI];
            }
            else {
                $standardK=$KData->fertilization_standard_amount;
            };
            if (empty($PData) || empty($PData->fertilization_standard_amount)) {
                if(gettype($fertilizationDivision)!="object") {
                    if ((array)$fertilizationDivision[0]) {
                        throw new GisException(trans('common.fertilization_division_not_found'), SystemCode::NOT_FOUND);
                    }
                }
                $standardP=$fertilizationDivision->p * $pkRatio[self::TYPE_KALI];
            }
            else {
                $standardP=$PData->fertilization_standard_amount;
            };
        } else {
            $KData = $this->_userFertilizerDefinitionDetailKalis;
            $PData = $this->_userFertilizerDefinitionDetailPhotphos;
            if(is_null($KData) || is_null($PData) || is_null($KData) || is_null($PData))
            {
                throw new GisException(trans('common.creation_fertilizer_map_not_found_p_k'), SystemCode::NOT_FOUND);
            }
            $standardK = $KData[0]->fertilization_standard_amount;
            $standardP = $PData[0]->fertilization_standard_amount;
        }

        return array(
            self::TYPE_KALI => $standardK > $npkExists[self::TYPE_KALI] ? $standardK - $npkExists[self::TYPE_KALI] : 0,
            self::TYPE_PHOTPHO => $standardP > $npkExists[self::TYPE_PHOTPHO] ? $standardP - $npkExists[self::TYPE_PHOTPHO] : 0
        );
    }

    /**
     * Get Fertilizer select by condition list
     *
     * @return multitype:string Ambigous <string, mixed, \Illuminate\Foundation\Application, \Illuminate\Container\static>
     */
    public function getMapSelectCondition()
    {
        return array(
            '' => trans('common.select_item_null'),
            '1' => trans('common.fertilizer_select_condition_all'),
            '2' => trans('common.fertilizer_select_condition')
        );
    }

    /**
     * Get ratio N-P-K with machine type
     *
     * @param Models\Entities\FertilizerMapProperty $fertilizerProperty
     *
     * @return array() npkRatio
     */
    public function getMachineData(FertilizerMapProperty $fertilizerProperty)
    {
        if ($fertilizerProperty->fertilizing_machine_type == self::FERTILIZER_MACHINE_ONE) {
            $machineData = array(
                array(
                    'one_barrel_fertilizer_name' => $fertilizerProperty->one_barrel_fertilizer_name,
                    'one_barrel_n' => $fertilizerProperty->one_barrel_n ? $fertilizerProperty->one_barrel_n : 0,
                    'one_barrel_p' => $fertilizerProperty->one_barrel_p ? $fertilizerProperty->one_barrel_p : 0,
                    'one_barrel_k' => $fertilizerProperty->one_barrel_k ? $fertilizerProperty->one_barrel_k : 0,
                    'fertilizer_type' => $fertilizerProperty->fertilizer_price_type == 1 ? '20' : '500',
                    'fertilizer_price' => $fertilizerProperty->fertilizer_price ? $fertilizerProperty->fertilizer_price : 0
                )
            );
        } else {
            $machineData = array(
                array(
                    'barrel' => trans('common.creating_map_table_2_row2'),
                    'fertilizer_name' => $fertilizerProperty->main_fertilizer_name,
                    'fertilizer_n' => $fertilizerProperty->main_fertilizer_n ?  $fertilizerProperty->main_fertilizer_n : 0,
                    'fertilizer_p' => $fertilizerProperty->main_fertilizer_p ?  $fertilizerProperty->main_fertilizer_p : 0,
                    'fertilizer_k' => $fertilizerProperty->main_fertilizer_k ? $fertilizerProperty->main_fertilizer_k : 0,
                    'fertilizer_type' => $fertilizerProperty->fertilizer_price_type == 1 ? '20' : '500',
                    'fertilizer_price' => $fertilizerProperty->fertilizer_price ? $fertilizerProperty->fertilizer_price : 0
                ),
                array(
                    'barrel' => trans('common.creating_map_table_2_row3'),
                    'fertilizer_name' => $fertilizerProperty->sub_fertilizer_name,
                    'fertilizer_n' => $fertilizerProperty->sub_fertilizer_n ?  $fertilizerProperty->sub_fertilizer_n : 0,
                    'fertilizer_p' => $fertilizerProperty->sub_fertilizer_p ? $fertilizerProperty->sub_fertilizer_p : 0,
                    'fertilizer_k' => $fertilizerProperty->sub_fertilizer_k ? $fertilizerProperty->sub_fertilizer_k : 0,
                    'fertilizer_type' => $fertilizerProperty->fertilizer_price_sub_type == 1 ? '20' : '500',
                    'fertilizer_price' => $fertilizerProperty->fertilizer_price_sub ? $fertilizerProperty->fertilizer_price_sub : 0
                )
            );
        }
        return $machineData;
    }

    /**
     * Get Organic matter field data of fertilizer map
     *
     * @param Models\Entities\OrganicMatterField $organicMatterField
     *
     * @return array() $organicMatterData
     */
    public function getOrganicMatterFieldData($organicMatterField)
    {
        $organicMatterData = array();
        for ($i = 0; $i <= 4; $i ++) {
            $index = $i + 1;
            if($i==0){
                $organicMatterData[$i] = array(
                    'type' => $index,
                    'organic_matter_field_type' => trans('common.creating_map_table_3_row1')
                );
            }
            else if($i==1){
                $organicMatterData[$i] = array(
                    'type' => $index,
                    'organic_matter_field_type' => trans('common.creating_map_table_3_row2')
                );
            }
            else if($i==2){
                $organicMatterData[$i] = array(
                    'type' => $index,
                    'organic_matter_field_type' => trans('common.creating_map_table_3_row3')
                );
            }
            else if($i==3){
                $organicMatterData[$i] = array(
                    'type' => $index,
                    'organic_matter_field_type' => trans('common.creating_map_table_3_row4')
                );
            }
            else{
                $organicMatterData[$i] = array(
                    'type' => $index,
                    'organic_matter_field_type' => trans('common.creating_map_table_3_row5')
                );
            }
            $organicMatterData[$i][self::TYPE_NITO] = "";
            $organicMatterData[$i][self::TYPE_KALI] = "";
            $organicMatterData[$i][self::TYPE_PHOTPHO] = "";
        }
        for($i = 0; $i < count($organicMatterField); $i++){
            $typeIndex = $organicMatterField[$i]->organic_matter_field_type;
            $typeIndex = $typeIndex - 1;
            $organicMatterData[$typeIndex][self::TYPE_NITO] = $organicMatterField[$i]->n;
            $organicMatterData[$typeIndex][self::TYPE_KALI] = $organicMatterField[$i]->k;
            $organicMatterData[$typeIndex][self::TYPE_PHOTPHO] = $organicMatterField[$i]->p;
        }

        return $organicMatterData;
    }

    /**
     * Get stages data of fertilizer map
     *
     * @param Models\Entities\FertilizerStage $fertilizerStage
     *
     * @return array() $stageData
     */
    public function getStageData($fertilizerStage)
    {
        $stageData = array();
        if ($fertilizerStage) {
            foreach ($fertilizerStage as $item) {
                $stageData[] = array(
                    'fertilization_stage' => $item->fertilization_stage,
                    self::TYPE_NITO => $item->n,
                    self::TYPE_KALI => $item->k,
                    self::TYPE_PHOTPHO => $item->p
                );
            }
        }
        $n = count($fertilizerStage);
        for($i=0;$i<(5-$n);$i++){
            array_push($stageData,array(
                'fertilization_stage' => null,
                self::TYPE_NITO => null,
                self::TYPE_KALI => null,
                self::TYPE_PHOTPHO => null
            ));
        }
        return $stageData;
    }

    /**
     * Prepare data for edit fertilzier map
     *
     * @param Models\Entities\FertilizerMap $fertilizerMap
     *
     * @return array() $fertilzerData
     */
    public function prepareDataEditMap(FertilizerMap $fertilizerMap)
    {
        $fertilizerProperty = $fertilizerMap->fertilizerMapProperty;
        $fertilizerStandard = FertilizerFacade::find($fertilizerProperty->fertilizer_standard_definition_id);
        return array(
            'crops_id' => $fertilizerProperty->crops_id,
            'fertilizing_machine_type' => $fertilizerProperty->fertilizing_machine_type,
            'machine' => $this->getMachineData($fertilizerProperty),
            'control_methodology' => $fertilizerProperty->control_methodology,
            'fixed_fertilizer_amount' => $fertilizerProperty->fixed_fertilizer_amount,
            'mesh_size' => $fertilizerProperty->mesh_size,
            'main_fertilizer_usual_amount' => $fertilizerProperty->main_fertilizer_usual_amount,
            'sub_fertilizer_usual_amount' => $fertilizerProperty->sub_fertilizer_usual_amount,
            'fertilizer_standard_definition_id' => $fertilizerProperty->fertilizer_standard_definition_id,
            'fertilizerStandard'=>$fertilizerStandard,
            'soil_analysis_type' => $fertilizerProperty->soil_analysis_type,
            'p' => $fertilizerProperty->p,
            'k' => $fertilizerProperty->k,
            'organic_matter' => $this->getOrganicMatterFieldData($fertilizerMap->organicMatterField),
            'stages' => $this->getStageData($fertilizerMap->fertilizerStage)
        );
    }


}

