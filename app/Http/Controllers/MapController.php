<?php
namespace Gis\Http\Controllers;

use Gis\Models\Entities\Crop;
use Gis\Models\Entities\FertilityMap;
use Gis\Models\Entities\MapInfo;
use Gis\Models\GeoTools;
use Gis\Models\MapTools;
use Gis\Models\Repositories\FertilizerMapPropertyFacade;
use Gis\Models\Repositories\FolderFacade;
use Gis\Models\Services\FertilityMapServiceFacade;
use Gis\Models\Repositories\FertilityMapFacade;
use Gis\Models\Repositories\CropFacade;
use Gis\Models\Services\FooterServiceFacade;
use Gis\Models\Services\UserServiceFacade;
use Gis\Models\Services\CropServiceFacade;
use Gis\Models\Services\FertilizerServiceFacade;
use Gis\Models\Services\MapServiceFacade;
use Gis\Exceptions\GisException;
use Gis\Models\SystemCode;
use Illuminate\Http\Request;
use Gis\Models\Services\FolderServiceFacade;
use Gis\Models\Services\HelpLinkServiceFacade;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;

class MapController extends Controller
{

    /**
     * Specify the selection area in the fertility map
     *
     * @param Request $request
     * @return the selected area in the fertility map
     */
    public function selection(Request $request)
    {
        $data = $request->all();
        if (empty($data['layer_id'])) {
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }
        //check whether request is prediction or not
        $this->isPredictionValid($data);
        $response = GeoTools::extractCoordinate($data);
        $currentMap =  FolderFacade::findById($data['layer_id']);
        if (empty($currentMap)) {
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }
        $parentLayer = FolderFacade::find($currentMap->parent_folder);
        if($parentLayer->is_recyclebin){
            throw new GisException(trans('common.parent_fertilizer_map_is_bin'));
        }

        if (! is_array($response) && ! $response) {
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }
        // Add application log
        ApplicationLogFacade::logActionMode2(LoggingAction::MODE2_SPECIFY_CONDITION_TO_CREATE_FERTILIZER_MAP, LoggingAction::MODE2_SPECIFY_CONDITION_TO_CREATE_FERTILIZER_MAP);
        return response()->json($response);
    }

    /**
     * check whether prediction request is valid or not
     * @param $data
     * @throws GisException
     */
    function isPredictionValid($data){
        if($data["vector"] == "true")
        {
            if (empty($data['fertilizer_width'])) {
                throw new GisException(trans('common.prediction_data_fertilizer_width_null'));
            }
            if (empty($data['field_width'])) {
                throw new GisException(trans('common.prediction_data_field_width_null'));
            }
            if (!is_numeric($data['fertilizer_width'])) {
                throw new GisException(trans('common.prediction_data_fertilizer_width_invalid'));
            }
            if (!is_numeric($data['field_width'])) {
                throw new GisException(trans('common.prediction_data_field_width_invalid'));
            }
            if (intval($data['fertilizer_width'])>100 || intval($data['fertilizer_width'])<1) {
                throw new GisException(trans('common.prediction_data_fertilizer_width_invalid'));
            }
            if (intval($data['field_width'])>500 || intval($data['field_width'])<1) {
                throw new GisException(trans('common.prediction_data_field_width_invalid'));
            }
            if(intval($data['fertilizer_width']) > intval($data['field_width'])){
                throw new GisException(trans('common.prediction_data_fertilizer_width_invalid'));
            }
        }
    }
    /**
     * get map information for export pdf
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showMapJson(Request $request)
    {
        $data = $request->all();
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_CLICK_LAYER,$data);
        return response()->json(GeoTools::extractGeoForExport($data));
    }

    /**
     * Get the properties of overall layers for fertilizer map
     *
     * @param Request $request
     * @return overall information of all layers for fertilizer map
     */
    public function showMap(Request $request)
    {
        $data = $request->all();
        $file['file_name'] = 'manytif1';
        $file['raster'] = 'raster25';
        $file['zoom'] = '11';
        $file['central'] =  [15919771.3214436,5334025.88236807];
        $file['central_50'] =  [15688687.2005229,5236559.64516434];
        $file['is_invisible_layer'] =  false ;

        if (! empty($data['userId'])) {
            //Show all fertility map for user
            $mapProperties = MapTools::showMap($data['userId']);
        } else  {
            if (! empty($data['layerId'])) {

                if (! empty($data['is_fertilizer'])) {
                    $mapProperties = MapTools::showMap(null, $data['layerId'], true);
                } else {
                    $mapProperties = MapTools::showMap(null, $data['layerId']);
                }
                $layers = FolderFacade::find($data['layerId']);
                $layerParent=FolderFacade::find($layers->parent_folder);
                if($layers->is_invisible_layer||$layerParent->is_recyclebin==true)
                    $mapProperties['is_invisible_layer'] = true ;
            }else  if (isset($data['layerDefault'])) {
                $layers = !empty($data['layerDefault']) ? FolderFacade::find($data['layerDefault']) : null;
                $file['scale_type'] =  1 ; // for click to call raster
                if ( $layers && round($layers->scale_type) == 2 ) {
                    $file['file_name'] = 'manytif2';
                    $file['raster'] = 'raster50';
                    $file['central'] =  $file['central_50'] ;
                    $file['scale_type'] = round($layers->scale_type) ;
                }
                return response()->json(json_encode([$file]));
            }
        }
        if (! empty($data['mode_selection_ids'])) {
            $mapProperties['selection'] = GeoTools::extractSelectionCoordinate($data['mode_selection_ids']);
        }
        //Add application log
        if (!empty($data['layerId'])) {
            ApplicationLogFacade::logAction(LoggingAction::ACTION_CLICK_LAYER,"layer id=".$data['layerId']);
        }
        return response()->json(json_encode($mapProperties));
    }

    /**
     * Create fertilizer map and display it in the browser
     *
     * @param $mapId the
     *            fertilizer id
     * @param int $cropId
     *            the crops id
     * @param Request $request
     *            the request to create fertilizer map
     * @return the fertilizer map information and also display it in the browser
     * @throws GisException will be thrown if fertility doesn't exit
     */
    public function creatingMap($mapId, $cropId = 0, Request $request)
    {
        $postData = $request->all();
        $mapInfoIds = $postData['mapInfoIds'];
        $user_id_main = $postData['user_id_main'];
        $listNitrogens = MapInfo::whereIn('id', $mapInfoIds)->lists('id');

        $user = UserServiceFacade::findById($user_id_main);
        $isGuest = $user->usergroup->is_guest_group ? true : false;
        $geoChoose = [];
        $map = FertilityMapFacade::findByField('id', $mapId)->first();
        if (! $map) {
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }

        $crops = array(
                '' => trans('common.select_item_null')
            ) + CropFacade::orderBy("order_number","ASC")->lists('crops_name', 'id')->all();
        $photphos = Array(
            '' => trans('common.select_item_null')
        );
        $kalis = Array(
            '' => trans('common.select_item_null')
        );

        $crop = CropFacade::getById($cropId);
        if (! $crop) {
            $crop = new Crop();
        }

        $fertilizerStandardData = FertilizerServiceFacade::getArrayFertilizerByCrop($cropId, $isGuest);
        $fertilizers = $fertilizerStandardData['data'];
        $initialData = $fertilizerStandardData['initial'];

        $fertilizerIds = array_keys($initialData);
        $initialId = ! empty($fertilizerIds[1]) ? $fertilizerIds[1] : $fertilizerIds[0];
        foreach ($initialData as $standardId => $initial_display) {
            if ($initial_display === true) {
                $initialId = $standardId;
                break;
            }
        }

        $helpLink = HelpLinkServiceFacade::findHelpLinkByAdd(url());
        //Add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_OPEN_FORM_TO_CREATE_FERTILIZER_MAP,$postData);
        return view('admin.map.map', compact('map', 'geoChoose', 'crops', 'crop', 'fertilizers', 'photphos', 'kalis', 'listNitrogens', 'user_id_main', 'initialId', 'helpLink'));
    }


    /**
     * Show the map in the browser
     *
     * @param int $mapId
     *            the id of fertility or fertilizer map
     * @param int $cropId
     *            the crops id
     * @return the map
     */
    public function openMapViewer($mapId = 0, $cropId = 0)
    {
        $mapInfoIds = null;
        $user_id_main = null;
        $listNitrogens = null;

        $geoChoose = [];
        $map = new FertilityMap();

        $crops = array(
                '' => trans('common.select_item_null')
            ) + CropFacade::orderBy("order_number","ASC")->lists('crops_name', 'id')->all();
        $photphos = Array(
            '' => trans('common.select_item_null')
        );
        $kalis = Array(
            '' => trans('common.select_item_null')
        );

        $crop = CropFacade::getById($cropId);
        if (! $crop) {
            $crop = new Crop();
        }

        $initialId = null;
        $fertilizerStandardData = FertilizerServiceFacade::getArrayFertilizerByCrop($cropId);
        $fertilizers = $fertilizerStandardData['data'];
        $initialData = $fertilizerStandardData['initial'];
        return view('admin.map.mapviewer', compact('map', 'geoChoose', 'crops', 'crop', 'fertilizers', 'photphos', 'kalis', 'listNitrogens', 'user_id_main', 'initialId'));
    }

    /**
     * get map confirm viewer
     *
     * @param Request $request
     *            info of the map
     * @return \Illuminate\View\View
     */
    public function getMapConfirm(Request $request)
    {
        $postData = $request->all();
        $fertilizerStandardCrop = FertilizerServiceFacade::getById($postData['fertilizer_standard_definition_id']);
        $fertility=FertilityMapServiceFacade::findById($postData['fertility_map_id']);
        $table3s = json_decode($postData['organic_matter_fields'], true);
        if (is_array($table3s) || is_object($table3s)) {
            foreach ($table3s as $index => $value) {
                if ($table3s[$index]['n'] == "")
                    $table3s[$index]['n'] = 0;
                if ($table3s[$index]['k'] == "")
                    $table3s[$index]['k'] = 0;
                if ($table3s[$index]['p'] == "")
                    $table3s[$index]['p'] = 0;
            }
        }
        $table4s = json_decode($postData['fertilization_stages'], true);
        if (is_array($table4s) || is_object($table4s)) {
            $count4 = count($table4s);
            for ($i = 0; $i < $count4; $i ++) {
                if ($table4s[$i]['fertilization_stage'] == "")
                    unset($table4s[$i]);
            }
            //reverse item of array
            $table4s= array_reverse($table4s);
        }
        if($postData['fertilizing_machine_type']==1){
            if($postData['fertilizer_price_type']==1)
                $postData['fertilizer_price']=$postData['fertilizer_price'].trans('common.fertilizer_table_price_unit_20');
            else
                $postData['fertilizer_price']=$postData['fertilizer_price'].trans('common.fertilizer_table_price_unit_500');
        }
        else{
            if($postData['fertilizer_price_type']==1)
                $postData['fertilizer_price']=$postData['fertilizer_price'].trans('common.fertilizer_table_price_unit_20');
            else
                $postData['fertilizer_price']=$postData['fertilizer_price'].trans('common.fertilizer_table_price_unit_500');
            if($postData['fertilizer_price_sub_type']==1)
                $postData['fertilizer_price_sub']=$postData['fertilizer_price_sub'].trans('common.fertilizer_table_price_unit_20');
            else
                $postData['fertilizer_price_sub']=$postData['fertilizer_price_sub'].trans('common.fertilizer_table_price_unit_500');

        }
        return view('admin.map.mapConfirm', compact('postData', 'fertilizerStandardCrop','fertility', 'table3s', 'table4s'));
    }

    /**
     * Retrieve the crops, photpho and kali for specified fertilizer
     *
     * @param $fertilizerId the
     *            id of fertilizer
     * @param $cropId the
     *            crops id
     * @return the detail information of fertilizer
     */
    public function getOptions($fertilizerId, $cropId)
    {
        $response = FertilizerServiceFacade::getUserStandardId($fertilizerId, $cropId);
        $photphos = FertilizerServiceFacade::getArrayPhotphos($fertilizerId, $cropId, $response['isSystem'], $response['userStandardId']);
        $kalis = FertilizerServiceFacade::getArrayKalis($fertilizerId, $cropId, $response['isSystem'], $response['userStandardId']);
        $array = Array();
        $array['photpho'] = $photphos;
        $array['kali'] = $kalis;
        return $array;
    }

    /**
     * Open the changing color screen to change color for specified fertilizer
     *
     * @param $fertilizerId the
     *            id of fertilizer
     * @return the form to change color for fertilizer
     */
    public function openChangingColor($fertilizerId)
    {
        $colors = FertilizerServiceFacade::getColorOfFertilizerMapInfo($fertilizerId);
        if (empty($colors)) {
            return response()->json(buildResponseMessage(trans('common.fertility_maps_not_exists'), 1, null, - 1));
        } else {
            $isOneBarrel = FertilizerServiceFacade::getControlMethodology($fertilizerId);
            return view('admin.map.changingcolor', [
                'colors' => $colors,
                'isOneBarrel' => $isOneBarrel
            ]);
        }
    }

    /**
     * Open form for user to change the color or volumn of fertilizer map
     * @param Request $request
     * @return mixed
     */
    public function openValueChangingColor(Request $request)
    {
        $postData = $request->all();
        $colors = FertilizerServiceFacade::getFertilizerMapInfoById($postData['color']);
        $isOneBarrel = FertilizerServiceFacade::getControlMethodology($postData['layerID']);
        return view('admin.map.changing_value_color', [
            'colors' => $colors,
            'rgb' => implode(',',$postData['rgbCode']),
            'layerID' => $postData['layerID'],
            'isOneBarrel' => $isOneBarrel
        ]);
    }

    /**
     * Save the changed color of fertilizer
     *
     * @param Request $request
     *            the color information of fertilizer
     * @return the fertilizer color information
     */
    public function submitChangingColor(Request $request)
    {
        $valid = FertilizerServiceFacade::validColorDetails($request->all());
        if ($valid) {
            return $valid;
        } else {
            FertilizerServiceFacade::saveColorDetails($request->all());
            return response()->json(buildResponseMessage(trans('common.save_success'), 1, null, -1));
        }
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_SUBMIT_CHANGING_COLOR,$request->all());
    }

    /**
     * Dispaly Form Merge color for some meshes of fertilizer
     *
     * @param Request $request
     *            the information of fertilizer
     * @return mixed
     */

    public function mergeMapColor(\Illuminate\Http\Request $request)
    {

        $fertilizers = $request->all();
        $colors= FertilizerServiceFacade::getColorCurrentFertilizerMap($fertilizers['fertilizerId']);
        $isOneBarrel = FertilizerServiceFacade::getControlMethodology($fertilizers['fertilizerId']);
        return view('admin.map.popup_merging',compact('colors','isOneBarrel'));

    }
     /**
     * Submit Form Merge color for some meshes of fertilizer
     *
     * @param Request $request
     *            the information of fertilizer
     * @return mixed
     */

    public function submitMergingMapColor(\Illuminate\Http\Request $request)
    {

        $fertilizers = $request->all();
        $colors= FertilizerServiceFacade::mergeColorCurrentFertilizerMap($fertilizers);
        return response()->json(buildResponseMessage(trans('common.save_success'), 1, null, -1));

    }


    /**
     * Merge color for some meshes of fertilizer
     *
     * @param Request $request
     *            the information of fertilizer
     * @return mixed
     */
    public function mergeDataMapColor(\Illuminate\Http\Request $request){

        $fertilizers = $request->all();
        $fertilizerId = $fertilizers['fertilizerId'];
        $mapInfoId = ! empty($fertilizers['mapInfoIds']) ? $fertilizers['mapInfoIds'] : null;
        $colorForFertilizerMap = FertilizerServiceFacade::getColorForFertilizerMap($fertilizerId, $mapInfoId);
        if(empty($colorForFertilizerMap)){
            return response()->json(buildResponseMessage(trans('common.fertility_maps_not_exists'), 1, null, -1));
        }
        $isOneBarrel = FertilizerServiceFacade::getControlMethodology($fertilizerId);
        $colors = $colorForFertilizerMap['colorLists'];
        return view('admin.map.mergingcolor', compact('colors', 'mapInfoId', 'fertilizerId','isOneBarrel'));

    }


    /**
     * Open form to edit color for a mesh of fertilizer
     *
     * @param $fertilizerId the
     *            fertilizer id
     * @param $colorIds the
     *            color id
     * @return mixed
     */
    public function saveMergingColor(\Illuminate\Http\Request  $request)
    {
         $status = FertilizerServiceFacade::saveMergingColor($request->all());
         return response()->json(buildResponseMessage(trans('common.save_success'), 1, null, -1));
    }

    /**
     * Save the edited color of fertilizer
     *
     * @param Request $request
     *            the information of fertilizer color
     * @return mixed
     */
    public function submitEditingColor(\Illuminate\Http\Request $request)
    {
        if (FertilizerServiceFacade::getIsBinFertilizerMap($request['fertilizerId'])) {
            return response()->json(buildResponseMessage(trans('common.parent_fertilizer_map_is_bin'), 1, null, -1));
        }

        ApplicationLogFacade::logAction(LoggingAction::ACTION_SUBMIT_CHANGING_COLOR, $request->all());

        //add application log
        $getColorExist = FertilizerServiceFacade::validColorListDetails($request->all());
        if (!empty($getColorExist)) {
            //trans('common.parent_fertilizer_map_is_bin'), 1, null, -1
            return response()->json($getColorExist);
        }else{
            return FertilizerServiceFacade::saveEditingColor($request->all());
        }



    }

    /**
     * Display the fertility map to the browser
     *
     * @param $user_id the
     *            id of user to get the fertility map
     * @return the information to show the fertility map
     */
    public function showNitoMap($user_id)
    {
        $user=UserServiceFacade::findById($user_id);
        $fertilizerFolder = FolderServiceFacade::getRandomParentFolder('fertilizer',$user->user_group_id);
        if (empty($fertilizerFolder))
            throw new GisException(trans('common.create_fertilizer_parent_not_found'), SystemCode::NOT_FOUND);
        $maps = FolderServiceFacade::getUserFertilityMaps($user_id);
        $crops = CropServiceFacade::getArrayCrops();
        $modes = MapServiceFacade::getMapSelectCondition();
        return view('map.nito', compact('maps', 'crops', 'modes'));
    }

    /**
     * Show S1 Screen for export PDF.
     *
     * @param
     *            $layer_id
     * @return $this
     */
    public function showExportMap($layer_id)
    {
        $systemInfo= FooterServiceFacade::loadFooter();
        $layer=FolderServiceFacade::findById($layer_id);
        if($layer->is_fertility_folder||$layer->is_admin_folder){
            $map = MapTools::showMap(null, $layer_id, false, true);
            ApplicationLogFacade::logAction(LoggingAction::ACTION_EXPORT_TO_PDF_FILE);
            return view('map.exportFertility', compact('map', 'systemInfo'));
        }
        else {
            $map = MapTools::showMap(null, $layer_id, true, true);
            $data = FertilizerServiceFacade::fertilizerPropetiesData($layer_id);
            $meshSize = $data->fertilizerMap->getMeshSize();
            $square = pow($meshSize, 2) / 1000;
            $area = number_format($data->fertilizerMap->getArea() / 10, 1);
            $data->main_sum = 0;
            $data->sub_sum = 0;
            $data->main_sub_sum = 0;
            $data->main_price = 0;
            $data->sub_price = 0;
            $data->main_sub_price = 0;
            foreach ($data->fertilizerMap->fertilizerMapInfo as $value) {
                $data->main_sum += $value->main_fertilizer;
                $data->sub_sum += $value->sub_fertilizer;
            }
            //new update price
            $data->main_sum = $data->main_sum * $square;
            $data->sub_sum = $data->sub_sum * $square;
            $data->main_sub_sum = $data->main_sum + $data->sub_sum;
            if ($data->main_sum != 0)
                $data->main_sum = round($data->main_sum);
            if ($data->sub_sum != 0)
                $data->sub_sum = round($data->sub_sum);
            if ($data->main_sub_sum != 0)
                $data->main_sub_sum = round($data->main_sub_sum);

            if ($data->fertilizerMapProperty->fertilizer_price_type == 1) {
                $data->main_price = (($data->main_sum * $data->fertilizerMapProperty->fertilizer_price) / 20);
                $data->fertilizerMapProperty->fertilizer_price = number_format($data->fertilizerMapProperty->fertilizer_price) . trans('common.fertilizer_table_price_unit_20');
            } else {
                $data->main_price = (($data->main_sum * $data->fertilizerMapProperty->fertilizer_price) / 500);
                $data->fertilizerMapProperty->fertilizer_price = number_format($data->fertilizerMapProperty->fertilizer_price) . trans('common.fertilizer_table_price_unit_500');
            }

            if ($data->fertilizerMapProperty->fertilizing_machine_type == 2) {
                if ($data->fertilizerMapProperty->fertilizer_price_sub_type == 1) {
                    $data->sub_price = (($data->sub_sum * $data->fertilizerMapProperty->fertilizer_price_sub) / 20);
                    $data->fertilizerMapProperty->fertilizer_price_sub = number_format($data->fertilizerMapProperty->fertilizer_price_sub) . trans('common.fertilizer_table_price_unit_20');
                } else {
                    $data->sub_price = (($data->sub_sum * $data->fertilizerMapProperty->fertilizer_price_sub) / 500);
                    $data->fertilizerMapProperty->fertilizer_price_sub = number_format($data->fertilizerMapProperty->fertilizer_price_sub) . trans('common.fertilizer_table_price_unit_500');
                }
                $data->main_sub_price = $data->main_price + $data->sub_price;

            }

            if ($data->main_sum != 0)
                $data->main_sum = number_format($data->main_sum);
            if ($data->sub_sum != 0)
                $data->sub_sum = number_format($data->sub_sum);
            if ($data->main_sub_sum != 0)
                $data->main_sub_sum = number_format($data->main_sub_sum);
            if ($data->main_price != 0)
                $data->main_price = number_format($data->main_price);
            if ($data->sub_price != 0)
                $data->sub_price = number_format($data->sub_price);
            if ($data->main_sub_price != 0)
                $data->main_sub_price = number_format($data->main_sub_price);
            //add application log
            ApplicationLogFacade::logAction(LoggingAction::ACTION_EXPORT_TO_PDF_FILE, $data);
            return view('map.export', compact('map', 'data', 'area', 'systemInfo'));
        }
    }

    /**
     * get the map list generate when user pay for so do bon phan.
     *
     * @param Request $request
     * @return array
     */
    public function showSelectionMap(\Illuminate\Http\Request $request)
    {
        $mapList = FertilityMapServiceFacade::getMapSelection($request->input('crop_id'),
            $request->input('fertility_map_id'), $request->input('user_id'));
        //Add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_MODE_SELECTION_CONDITION,$request);
        return response()->json(json_encode($mapList));
    }

    /**
     * Save the map for Guest user
     * @param Request $request
     * @return mixed
     */
    public function storeGuestMap(\Illuminate\Http\Request $request)
    {
        $data = $request->all();
        return response()->json(GeoTools::storeGuestMap($data));
    }

    /**
     * Predict shortage location of fertilizer
     * and show the information to end-user
     * @param Request $request
     * @return mixed
     */
    public function generateGuessDirection(\Illuminate\Http\Request $request)
    {
        $data = $request->all();
        $predictionData = GeoTools::generateGuessDirection($data);
        $predictionData=$this->buildPredictionData($predictionData,$data['fertilizer_map_id']);
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_PREDICT_SHORTAGE_LOCATION_FERTILIZER,$data);
        return response()->json($predictionData);
    }

    /**
     * Show map to user
     * @param Request $request
     * @return mixed
     */
    public function displayFinal(\Illuminate\Http\Request $request)
    {
        $data = $request->all();
        $predictionData = GeoTools::displayFinal($data);
        //add Application log.
        return response()->json($predictionData);
    }

    /**
     * Sort the data of fertilizer map
     * @param $a
     * @param $b
     * @return int
     */
    function cmp($a, $b){
        $arrA = explode(",",$a);
        $arrB = explode(",",$b);
        $af = floatval($arrA[4]);
        $bf = floatval($arrB[4]);
        if ($af == $bf) {
            return 0;
        }
        return ($af > $bf) ? -1 : +1;
    }
    /**
     * Help end-user to build the location shortage of fertilizers
     * @param $arrData the array of data that contains the main & sub fertilizer and percentage of them
     */
    function buildPredictionData($arrData,$fertilizerMapId){
        $fertilizerMap = FertilizerMapPropertyFacade::findByField('fertilizer_map_id',$fertilizerMapId)->first();
        $meshSize = floatval($fertilizerMap->mesh_size);
        $arrVolumeOfMainAndSubFertilizer = array();
        foreach($arrData as $arrRows){
            //arrRows contains following properties
            //-0: endpoint: is an array
            //-1: geo: is an array
            //-2: len: is a float
            //-3: distance: is a float
            //-4:mix: is an array and each element of the array is a string
            //--- for instance: "0.30,69897,400.00,0.00"
            //--- the 1st string "0.30" is the percentage of volume for both main&sub fertilization
            //-- the 2nd string: "69897" is the id of fertilizer_map_infos table
            //-- the 3rd string: "400.00" is the volume of main fertilizer and equivalents to main_fertilizer field
            //-- in the fertilizer_map_infos table
            //-- the 4rd string: "0.00" is the volume of sub fertilizer and equivalents to sub_fertilizer field
            //-- the 5rd string: "32.34345" is the range of mesh to arrow
            //-- in the fertilizer_map_infos table
            //get mix data, it's an array as described above
            $mixs = $arrRows["mix"];
            //Calculate total of main & sub fertilizer including percentage value
            $totalOfMainFertilizer = 0;
            $totalOfSubFertilizer =0;
            //area of one mesh with a unit
            $oneMesh = $meshSize*$meshSize/100;
            //array of main & sub & mesh id for each mesh
            //to mark the shortage location of fertilizer
            $arrEchMesh = array();
            usort($mixs, array($this, "cmp"));
            foreach($mixs as $element){
                //the value of $element is a string can be "0.30,69897,400.00,0.00,32.34345";
                $arr = explode(",",$element);
                $totalOfMainFertilizer = $totalOfMainFertilizer+
                    floatval($arr[0])*$oneMesh*floatval($arr[2]);
                $totalOfSubFertilizer = $totalOfSubFertilizer+floatval($arr[0])*$oneMesh*floatval($arr[3]);
                array_push($arrEchMesh,
                    array(
                        "main"=>(floatval($arr[0])*$oneMesh*floatval($arr[2]))/10,
                        "sub"=>floatval(($arr[0])*$oneMesh*floatval($arr[3]))/10,
                        "id"=>$arr[1],
                    )
                );
            }
            $totalMain = ($totalOfMainFertilizer)/10;
            $totalSub = ($totalOfSubFertilizer)/10;
            //push total of main & sub fertilizer to out put data
            array_push($arrVolumeOfMainAndSubFertilizer,array(
                "main"=>$totalMain,
                "sub"=>$totalSub,
                "geo"=>$arrRows["geo"],
                "detail_info" =>$arrEchMesh
            ));
            unset($arrEchMesh[0]);
        }
        return [
            'predictionData' => $arrVolumeOfMainAndSubFertilizer,
            'data' => $arrData
        ];
    }
    /**
     * User confirms the specified data to create new a fertilizer map
     *
     * @param Request $request
     *            the properties of fertilizer
     * @return mixed
     */
    public function confirmData(\Illuminate\Http\Request $request)
    {
        $postData = $request->all();
        $layer = MapServiceFacade::confirmMapData($postData);
        $layer->isCreate=$postData['isCreate'];
        if($postData['isCreate']==1)
            return response()->json(buildResponseMessage(sprintf(trans('common.confirm_map_created_success'),$layer->name), SystemCode::SUCCESS, null, $layer));
        else
            return response()->json(buildResponseMessage(sprintf(trans('common.confirm_map_created_success'),$layer->name), SystemCode::SUCCESS, null, $layer));
    }

    /**
     * Perform to validate data when specify parameter for a new fertilizer
     * User selects mode to create new a fertilizer
     *
     * @param Request $request
     *            the parameters of fertilizer
     * @return mixed
     */
    public function validateSpecificationFertilizer(\Illuminate\Http\Request $request)
    {
        $postData = $request->all();
        //Application log
        ApplicationLogFacade::logAction ( LoggingAction::ACTION_MODE_SELECTION_ALL,$postData);
        return response()->json(buildResponseMessage(trans('common.confirm_map_save_success'), SystemCode::SUCCESS));
    }

    /**
     * Open form for user to edit a fertilizer map
     * @param $fertilizerMapId
     * @return mixed
     * @throws GisException
     */
    public function editFertilizerMap($fertilizerMapId)
    {

        $fertilizerMap = FertilizerServiceFacade::getFertilizerMapById($fertilizerMapId);
        $map = $fertilizerMap->fertilityMap;
        $user_id_main = $fertilizerMap->user_id;
        $geoChoose = [];
        $crops = array(
                '' => trans('common.select_item_null')
            ) + CropFacade::orderBy("order_number","ASC")->lists('crops_name', 'id')->all();
        $photphos = Array(
            '' => trans('common.select_item_null')
        );
        $kalis = Array(
            '' => trans('common.select_item_null')
        );
        $crop = CropFacade::getById($fertilizerMap->fertilizerMapProperty->crops_id);
        if (! $crop) {
            $crop = new Crop();
        }
        $user = UserServiceFacade::findById($user_id_main);
        $isGuest = $user->usergroup->is_guest_group ? true : false;
        $fertilizerStandardData = FertilizerServiceFacade::getArrayFertilizerByCrop($fertilizerMap->fertilizerMapProperty->crops_id, $isGuest);
        $fertilizers = $fertilizerStandardData['data'];

        $listNitrogens = array();
        $fertilitiMapSelectionInfo = $fertilizerMap->FertilityMapSelection->fertilityMapSelectionInfo;
        foreach ($fertilitiMapSelectionInfo as $info) {
            array_push($listNitrogens, $info->map_info_id);
        }
        $isRecreate = true;
        $resultData=MapServiceFacade::prepareDataEditMap($fertilizerMap);
        $initData = json_encode($resultData);
        if($resultData['fertilizerStandard']->not_available){
            throw new GisException(trans('common.fertilizer_not_avalable'),SystemCode::NOT_FOUND);
        }
        $listNitrogens = json_encode($listNitrogens);
        return view('admin.map.edit_map', compact('map', 'geoChoose', 'crops',
            'crop', 'fertilizers', 'photphos', 'kalis',
            'listNitrogens', 'user_id_main',
            'helpLink', 'fertilizerMap', 'initData','isRecreate'));
    }
}
