<?php
namespace Gis\Http\Controllers;

use Gis\Models\Repositories\StandardCropFacade;
use Gis\Models\Services\FertilizerServiceFacade;
use Gis\Models\SystemCode;
use Illuminate\Http\Request;
use Gis\Models\Services\UserServiceFacade;
use Gis\Http\Requests\FertilizerRequest;
use Gis\Http\Requests\StandardCropRequest;
use Gis\Models\Entities\Fertilizer;
use Gis\Exceptions\GisException;
use Illuminate\Pagination\Paginator;
use Gis\Models\Entities\StandardCrop;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;

/**
 * Use this class to handle all the businesses
 * Class FertilizerController
 *
 * @package Gis\Http\Controllers
 */
class FertilizerController extends Controller
{

    /**
     * Open fertilizer list screen
     *
     * @return Response
     */
    public function openFertilizerList()
    {
        // echo session('user');exit;
        return view('admin.fertilizer.fertilizerList');
    }

    /**
     * Get the list of fertilizers
     *
     * @param Request $request
     * @return mixed
     */
    public function getFertilizers(\Illuminate\Http\Request $request)
    {
        $paggingRequest = $request->all();
        $postData = array();
        $reponse = FertilizerServiceFacade::getFertilizers($postData, $paggingRequest);
        return $reponse;
    }

    /**
     * Get the fertilizers by crops id and user id
     *
     * @param $cropId the
     *            id of the crops
     * @param $userId the
     *            id of the user
     * @return the list of fertilizers
     */
    public function getListFertilizers($cropId, $userId)
    {
        $user = UserServiceFacade::findById($userId);
        $isGuest = $user->usergroup->is_guest_group ? true : null;
        $fertilizers = FertilizerServiceFacade::getArrayFertilizerByCrop($cropId, $isGuest);
        return $fertilizers;
    }

    /**
     * Get the standard fertilizers by crops id and user id
     *
     * @param $cropId the
     *            id of the crops
     * @param $userId the
     *            id of the user
     * @return the list of fertilizers
     */
    public function getListStandardFertilizers($cropId, $userId, $standardFertilizer)
    {
        $user = UserServiceFacade::findById($userId);
        $isGuest = $user->usergroup->is_guest_group ? true : null;
        $fertilizers = FertilizerServiceFacade::getArrayStandardFertilizerByCrop($cropId, $isGuest, $standardFertilizer);
        return $fertilizers;
    }

    /**
     * Delete the fertilizers base on the posted ids
     *
     * @param Request $request
     * @return mixed
     */
    public function deleteFertilizers(Request $request)
    {
        $postData = $request->all();
        $ids = explode(',', $postData['fertilizer_ids']);

        $result = FertilizerServiceFacade::deleteFertilizers($ids);
        return $result;
    }

    /**
     * Get all the information of fertilizer
     *
     * @return fertilizer info
     */
    public function openFertilizerInfo()
    {
        $fertilizer = new Fertilizer();
        $fertilizer->created_by = - 1; // Does not set value for intance
        return view('admin.fertilizer.fertilizerInfo', compact('fertilizer'));
    }

    /**
     * Get the fertilizer by the id of fertilizer to update
     *
     * @param $fertilizerId id
     *            of fertilizer to edit
     * @return information of fertilizer
     * @throws GisException will be thrown if it doesn't exit in the application
     */
    public function editFertilizer($fertilizerId)
    {
        $fertilizer = FertilizerServiceFacade::getById($fertilizerId);
        if (! $fertilizer) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }

        // created_by: 0:public; 1-admin; 2-user
        if ($fertilizer->created_by == 0 && ! session('user')->usergroup->auth_authorization) {
            throw new GisException(trans('common.fertilizer_can_not_edit_admin'));
        }

        return view('admin.fertilizer.fertilizerInfo', compact('fertilizer'));
    }

    /**
     * Get popup for input system fertilizer information to copy
     *
     * @param $fertilizerId id
     *            of fertilizer to edit
     * @return popup for input system fertilizer information to copy
     * @throws GisException will be thrown if it doesn't exit in the application
     */
    public function copySystemFertilizerPopup($fertilizerId)
    {
        $fertilizer_get = FertilizerServiceFacade::getById($fertilizerId);
        $crops = FertilizerServiceFacade::getArrayCropExists($fertilizerId);
        $fertilizers = FertilizerServiceFacade::getArrayNormalFertilizer();
        $user = session('user');
        if (! $fertilizer_get) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }
        return view('admin.fertilizer.copy_system_standard_crop', compact('fertilizer_get', 'fertilizers', 'crops', 'user'));
    }

    /**
     * Copy data from System fertilization
     * @param Request $request
     * @return mixed
     */
    public function copySystemFertilizer(Request $request)
    {
        $postData = $request->all();
        $postData['user_code'] = session('user')->user_code;
        return FertilizerServiceFacade::copyFromSystemFertilizer($postData);
    }

    /**
     * Get the fertilizer information by the id of fertilizer
     *
     * @param $fertilizerId id
     *            of fertilizer
     * @return Fertilizer
     */
    public function getFertilizer($fertilizerId, $cropId)
    {
        $fertilizer = FertilizerServiceFacade::getById($fertilizerId);
        $standardCrop = FertilizerServiceFacade::getListFertilizerStandardIds($cropId);
        $check = false;
        foreach($standardCrop as $key){
            if(strcmp($fertilizer->fertilization_standard_name, $key->fertilization_standard_name)==0){
                $check = true;
                break;
            }
        }
        if (! $fertilizer) {
            $fertilizer = new Fertilizer();
        }
        if($check == true){
            return $fertilizer;
        }else return 0;

    }

    /**
     * Save the fertilizer to the database
     *
     * @param FertilizerRequest $request
     *            the information of fertilizer to save
     * @return the message that indicates the processing is successful or not
     */
    public function submitFertilizer(FertilizerRequest $request)
    {
        $postData = $request->all();
        $result = FertilizerServiceFacade::saveFertilizer($postData);
        return $result;
    }

    /**
     * Clone to another fertilizer from current fertilizer
     *
     * @param Request $request
     *            the fertilizer to be cloned
     * @return the information of the clone fertilizer
     */
    public function copyFertilizer(Request $request)
    {
        $postData = $request->all();
        $fertilizer = FertilizerServiceFacade::getById($postData['hidden_fertilizer_id']);
        if (! $fertilizer) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }
        if (isset($postData['created_by']))
            return view('admin.fertilizer.copy_system_standard_crop');
        else
            $result = FertilizerServiceFacade::copyFertilizer($postData);
        return $result;
    }

    /**
     * Get all the users that're associated with the fertilizer
     *
     * @param $fertilizerId the
     *            id of fertilizer
     * @return the list of users
     */
    public function openSpecifyUser($fertilizerId)
    {
        $fertilizer = FertilizerServiceFacade::getById($fertilizerId);
        if (! $fertilizer) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }
        $standardUserCodesList = FertilizerServiceFacade::getArrayStandardUserCodes($fertilizerId);

        $array = array();
        array_push($array, '1'); // Insert pager in to firt of array
        for ($i = 0; $i < sizeof($standardUserCodesList); $i ++) {
            array_push($array, $standardUserCodesList[$i]);
        }

        $standardUserCodes = implode(",", $array);
        $userGroups = UserServiceFacade::getArrayGroups();

        return view('admin.fertilizer.specifyUser', compact('fertilizerId', 'standardUserCodes', 'userGroups'));
    }

    /**
     * Get the list of users base on the search condition
     *
     * @param $searchModel the
     *            search condition
     * @param Request $request
     * @return the list of users
     */
    public function getSpecifyUsers($searchModel, Request $request)
    {
        $query = $searchModel;
        $pagingRequest = $request->all();
        Paginator::currentPageResolver(function () use($pagingRequest)
        {
            return $pagingRequest['page'];
        });

        return response()->json(UserServiceFacade::gridGetAll('specifyusers', $pagingRequest, json_decode($query, true)));
    }

    /**
     * Associate users with current fertilizer
     * User can access the fertilizer when it's associated with fertilizer
     *
     * @param Request $request
     *            the information of users
     * @return mixed
     */
    public function submitSpecifyUser(Request $request)
    {
        $postData = $request->all();

        $result = FertilizerServiceFacade::saveStandardUser($postData);

        return $result;
    }

    // BEGIN STANDARD CROP LIST

    /**
     * Get the list of crops for the current fertilizer
     *
     * @param $standardId the
     *            id of fertilizer
     * @return the list of crops that'are associated with fertilizer
     * @throws GisException will be thrown when the fertilizer doesn't exist
     */
    public function openStandardCropList($standardId)
    {
        $fertilizer = FertilizerServiceFacade::getById($standardId);
        if (! $fertilizer) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }
        return view('admin.fertilizer.standardCropList', compact('fertilizer'));
    }

    /**
     * Get the list of crops by fertilizer id for Admin role
     *
     * @param $standardId the
     *            id of fertilizer
     * @return the fertilizer and list of crops
     * @throws GisException will be thrown when the fertilizer doesn't exist
     */
    public function openSystemStandardCropAdmin($standardId)
    {
        $fertilizer = FertilizerServiceFacade::getById($standardId);
        $crops = FertilizerServiceFacade::getArrayAllCrops();
        $user = session('user');
        if (! $fertilizer) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }
        return view('admin.fertilizer.systemStandardCrop', compact('fertilizer', 'crops', 'user'));
    }

    /**
     * Get the list of crops by fertilizer id
     *
     * @param $standardId the
     *            id of fertilizer
     * @param Request $request
     * @return the list of crops
     */
    public function getStandardCrops($standardId, Request $request)
    {
        $query = null;
        $array = array(
            'fertilizer_standard_definition_id' => $standardId
        );
        $pagingRequest = $request->all();

        $result = UserServiceFacade::gridGetAll('standartcrops', $pagingRequest, $array);
        return $result;
    }

    /**
     * Get the detail information of crops of system fertilizer
     *
     * @param $fertilizerStandardId the
     *            id of fertilizer
     * @param $standardCropId the
     *            id of crops
     * @param Request $request
     * @return the detail information of each crops
     */
    public function getSystemStandardCropDetails($fertilizerStandardId, $standardCropId, Request $request)
    {
        $query = null;
        $paggingRequest = $request->all();
        $reponse = FertilizerServiceFacade::getSystemStandardCropDetails($fertilizerStandardId, $standardCropId, $paggingRequest);
        return $reponse;
    }
    /**
     * Clear the detail information of crops of system fertilizer
     *
     * @param $fertilizerStandardId the
     *            id of fertilizer
     * @param $standardCropId the
     *            id of crops
     * @param Request $request
     * @return the detail information of each crops
     */
    public function clearSystemStandardCropDetails($fertilizerStandardId, $standardCropId, Request $request)
    {
        $query = null;
        $paggingRequest = $request->all();
        $reponse = FertilizerServiceFacade::clearSystemStandardCropDetails($fertilizerStandardId, $standardCropId, $paggingRequest);
        return $reponse;
    }

    /**
     * Delete the crops by crops id
     *
     * @param Request $request
     * @return the information that indicates the processing is successful or not
     */
    public function deleteStandardCrops(Request $request)
    {
        $postData = $request->all();
        $ids = explode(',', $postData['standard_crop_ids']);
        $result = FertilizerServiceFacade::deleteStandardCrops($ids);
        return $result;
    }

    /**
     * Copy the crops of one fertilizer to another fertilizer
     *
     * @param Request $request
     *            the request information that contains crops id and detail info of crops
     * @return mixed
     */
    public function copyStandardCrop(Request $request)
    {
        $postData = $request->all();

        $standardCropId = $postData['standard_crop_id'];
        $distinationStandardId = $postData['destination_standard_id'];

        $result = FertilizerServiceFacade::copyStandardCrop($standardCropId, $distinationStandardId);
        return $result;
    }

    /**
     * Open crops information
     *
     * @param $standardId the
     *            id fertilizer to get the list of crops
     * @return the list of crops and fertilizer info
     */
    public function openStandardCropInfo($standardId)
    {
        $standardCrop = new StandardCrop();
        $crops = FertilizerServiceFacade::getArrayCrops($standardId);

        $fertilizer = FertilizerServiceFacade::getById($standardId);
        return view('admin.fertilizer.standardCropInfo', compact('standardCrop', 'crops', 'fertilizer'));
    }

    /**
     * Get the detail information of crops to edit
     *
     * @param $standardCropId the
     *            id of crops to be edited
     * @return the information of crops
     * @throws GisException will be thrown when the crops doesn't exist
     */
    public function editStandarCropInfo($standardCropId)
    {
        $standardCrop = FertilizerServiceFacade::getStandardCropById($standardCropId);
        if (! $standardCrop) {
            throw new GisException(trans('common.standardcrop_not_exists'));
        }
        $crops = FertilizerServiceFacade::getArrayCrops($standardCrop->fertilizer_standard_definition_id, $standardCrop->crops_id);
        $fertilizer = FertilizerServiceFacade::getById($standardCrop->fertilizer_standard_definition_id);
        return view('admin.fertilizer.standardCropInfo', compact('standardCrop', 'crops', 'fertilizer'));
    }

    /**
     * Save the information of crops to the database
     *
     * @param StandardCropRequest $request
     *            the information of crops to be updated
     * @return the message that indicates the processing is successful or not
     */
    public function submitStandardCropInfo(StandardCropRequest $request)
    {
        $postData = $request->all();

        $result = FertilizerServiceFacade::saveStandardCrop($postData);

        return $result;
    }

    /**
     * Save the detail information of crops to the database
     *
     * @param Request $request
     *            the detail information of crops to be updated
     * @return the amount of nitrogen
     */
    public function submitStandardCropDetails(Request $request)
    {
        $postData = $request->all();

        $result = FertilizerServiceFacade::saveStandardCropDetails($postData);

        return $result;

        $list = json_decode($postData['data']);
        return $list[0]->nito_amount;
    }

    /**
     * Save the detail information of crops for System fertilizer
     *
     * @param Request $request
     *            the detail information of crops to be saved
     * @return the amount of nitrogen
     */
    public function submitSystemStandardCropDetails(Request $request)
    {
        $postData = $request->all();
        $result = FertilizerServiceFacade::saveSystemStandardCropDetails($postData);

        return $result;
    }

    /**
     * Get the information of fertilizer in order to copy
     *
     * @param $standardCropId the
     *            id of fertilizer
     * @return overall information of specified fertilizer
     * @throws GisException will be thrown when fertilizer doesn't exist
     */
    public function openStandardCropCopying($standardCropId)
    {
        $standardCrop = FertilizerServiceFacade::getStandardCropById($standardCropId);
        if (! $standardCrop) {
            throw new GisException(trans('common.standardcrop_not_exists'));
        }
        $listFertilizer=FertilizerServiceFacade::getArrayNormalFertilizer();
        $crops = FertilizerServiceFacade::getArrayCrops($standardCrop->fertilizer_standard_definition_id, $standardCrop->crops_id);
        $fertilizer = FertilizerServiceFacade::getById($standardCrop->fertilizer_standard_definition_id);
        unset($listFertilizer[$fertilizer->id]);
        return view('admin.fertilizer.standardCropCopying', compact('standardCrop', 'crops', 'fertilizer','listFertilizer'));
    }

    /**
     * Open the detail form of crops
     *
     * @param $standardCropId the
     *            id of crops
     * @return the information of crops
     */
    public function openStandardCropDetail($standardCropId)
    {
        $standardCrop = FertilizerServiceFacade::getStandardCropById($standardCropId);
        if (! $standardCrop) {
            throw new GisException(trans('common.standardcrop_not_exists'));
        }
        return view('admin.fertilizer.standardCropDetail', compact('standardCropId'));
    }

    /**
     * Get the detail information of one crops
     *
     * @param $standardCropId the
     *            id of crops
     * @param Request $request
     * @return the detail information of crops
     */
    public function getStandardCropDetails($standardCropId, Request $request)
    {
        $paggingRequest = $request->all();
        $reponse = FertilizerServiceFacade::getStandardCropDetails($standardCropId, $paggingRequest);
        return $reponse;
    }

    /**
     * The function to implement autocomplete
     *
     * @param Request $request
     * @return the list of data
     */
    public function ajaxAutocomplete(\Illuminate\Http\Request $request)
    {
        $postData = $request->all();
        $keyword = $postData['keyword'];
        $standardId = $postData['data'];
        $users = FertilizerServiceFacade::findFertilizerByKeyword($keyword, $standardId);
        return $users;
    }

    /**
     * User performs to view the fertilizer map properties
     *
     * @param $id the
     *            id of fertilizer to download
     * @return the view of fertilizer map properties
     */
    public function fertilizerPropetiesView($layerid)
    {
        $data = FertilizerServiceFacade::fertilizerPropetiesData($layerid);
        $mesh_size = $data->fertilizerMapProperty->mesh_size;
        $square = pow($mesh_size,2)/1000;
        if (empty($data->fertilizerStandardDefinition)) {
            $data->fertilizerStandardDefinition = (object) array(
                'fertilization_standard_name' => null,
                'range_of_application' => null,
                'notes' => null,
                'remarks' => null
            );
        }
        $n = 0;
        $p = 0;
        $k = 0;
        $existOrganicMatterType = array();
        foreach ($data->fertilizerMap->organicMatterField as $value) {
            array_push($existOrganicMatterType, $value->organic_matter_field_type);
        }
        for ($i = 1; $i <= 4; $i ++) {
            if (! in_array($i, $existOrganicMatterType)) {
                $data->fertilizerMap->organicMatterField[] = (object) array(
                    'organic_matter_field_type' => $i,
                    'n' => 0,
                    'p' => 0,
                    'k' => 0
                );
            }
        }
        foreach ($data->fertilizerMap->organicMatterField as &$value) {
            $value->n = number_format($value->n, 1, '.', '');
            $value->p = number_format($value->p, 1, '.', '');
            $value->k = number_format($value->k, 1, '.', '');
            switch ($value->organic_matter_field_type) {
                case ("1"):
                {
                    $value->organic_matter_field_type = trans('common.organic_matter_type1');
                    $n += $value->n;
                    $p += $value->p;
                    $k += $value->k;
                    break;
                }
                case ("2"):
                {
                    $value->organic_matter_field_type = trans('common.organic_matter_type2');
                    $n += $value->n;
                    $p += $value->p;
                    $k += $value->k;
                    break;
                }
                case ("3"):
                {
                    $value->organic_matter_field_type = trans('common.organic_matter_type3');
                    $n += $value->n;
                    $p += $value->p;
                    $k += $value->k;
                    break;
                }
                case ("4"):
                {
                    $value->organic_matter_field_type = trans('common.organic_matter_type4');
                    $n += $value->n;
                    $p += $value->p;
                    $k += $value->k;
                    break;
                }
            }
        }
        $data->fertilizerMap->organicMatterField->sum = trans('common.fertilizer_table_sum');
        $data->fertilizerMap->organicMatterField->n_sum = number_format($n, 1, '.', '');
        $data->fertilizerMap->organicMatterField->p_sum = number_format($p, 1, '.', '');
        $data->fertilizerMap->organicMatterField->k_sum = number_format($k, 1, '.', '');
        $NStage = 0;
        $PStage = 0;
        $KStage = 0;
        foreach ($data->fertilizerMap->fertilizerStage as &$value) {
            $NStage += $value->n;
            $PStage += $value->p;
            $KStage += $value->k;
        }
        $data->fertilizerMap->fertilizerStage->sum = trans('common.fertilizer_table_sum');
        $data->fertilizerMap->fertilizerStage->n_sum = number_format($NStage, 1, '.', '');
        $data->fertilizerMap->fertilizerStage->p_sum = number_format($PStage, 1, '.', '');
        $data->fertilizerMap->fertilizerStage->k_sum = number_format($KStage, 1, '.', '');
        //reverse fertilization stage to display

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
        $data->main_sum = $data->main_sum*$square;
        $data->sub_sum = $data->sub_sum*$square;

        $data->main_sub_sum = $data->main_sum + $data->sub_sum;
        if ($data->main_sum != 0)
            $data->main_sum = round($data->main_sum);
        if ($data->sub_sum != 0)
            $data->sub_sum = round($data->sub_sum);
        if ($data->main_sub_sum != 0)
            $data->main_sub_sum = round($data->main_sub_sum);

        if ($data->fertilizerMapProperty->fertilizer_price_type == 1) {
            $data->main_price = (($data->main_sum * $data->fertilizerMapProperty->fertilizer_price) / 20);
            $data->fertilizerMapProperty->fertilizer_price =
                number_format(round($data->fertilizerMapProperty->fertilizer_price)).trans('common.fertilizer_table_price_unit_20');
        }

        else {
            $data->main_price = (($data->main_sum * $data->fertilizerMapProperty->fertilizer_price) / 500);
            $data->fertilizerMapProperty->fertilizer_price =
                number_format(round($data->fertilizerMapProperty->fertilizer_price)).trans('common.fertilizer_table_price_unit_500');
        }

        if ($data->fertilizerMapProperty->fertilizing_machine_type == 2) {
            if ($data->fertilizerMapProperty->fertilizer_price_sub_type == 1){
                $data->sub_price = (($data->sub_sum * $data->fertilizerMapProperty->fertilizer_price_sub) / 20);
                $data->fertilizerMapProperty->fertilizer_price_sub=
                    number_format(round($data->fertilizerMapProperty->fertilizer_price_sub)).trans('common.fertilizer_table_price_unit_20');
            }
            else {
                $data->sub_price = (($data->sub_sum * $data->fertilizerMapProperty->fertilizer_price_sub) / 500);
                $data->fertilizerMapProperty->fertilizer_price_sub=
                    number_format(round($data->fertilizerMapProperty->fertilizer_price_sub)).trans('common.fertilizer_table_price_unit_500');
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
            $data->main_price = number_format(round($data->main_price));
        if ($data->sub_price != 0)
            $data->sub_price = number_format(round($data->sub_price));
        if ($data->main_sub_price != 0)
            $data->main_sub_price = number_format(round($data->main_sub_price));
        $response = FertilizerServiceFacade::getUserStandardId($data->fertilizerMap->fertilizerMapProperty->fertilizer_standard_definition_id, $data->fertilizerMap->fertilizerMapProperty->crops_id);
        $photphos = FertilizerServiceFacade::getArrayPhotphos($data->fertilizerMap->fertilizerMapProperty->fertilizer_standard_definition_id, $data->fertilizerMap->fertilizerMapProperty->crops_id, $response['isSystem'], $response['userStandardId']);
        $kalis = FertilizerServiceFacade::getArrayKalis($data->fertilizerMap->fertilizerMapProperty->fertilizer_standard_definition_id, $data->fertilizerMap->fertilizerMapProperty->crops_id, $response['isSystem'], $response['userStandardId']);

        // Add application log
        ApplicationLogFacade::logActionMode2(LoggingAction::MODE2_VIEW_FERTILIZER_PROPERTIES, $data);
        return view('admin.map.fertilizer_properties', compact('data', 'photphos', 'kalis'));
    }

    /**
     * Show the fertilization prediction
     * @param $layer_id
     * @return mixed
     * @throws GisException
     */
    public function fertilizationOutPrediction($layer_id)
    {
        //Add application log
        ApplicationLogFacade::logActionMode2(LoggingAction::ACTION_OPEN_PREDICTION_FORM, "layer id =".$layer_id);
        //get the fertilizing_machine_type to indicate that's one or two barrels
        $fertilizerMap = FertilizerServiceFacade::getFertilizerByLayerId($layer_id);
        if($fertilizerMap==NULl){
            throw new GisException(trans("common.fertilizer_map_not_found"),SystemCode::NOT_FOUND);
        }
        $fertilizingMachineType = $fertilizerMap->FertilizerMapProperty->fertilizing_machine_type;
        return view('admin.fertilizer.fertilizationOutPrediction', compact('layer_id','fertilizingMachineType'));
    }
    // END STANDARD CROP LIST

    /**
     * popup fertilization out predict
     */
    public function fertilizationPredictPopup($barrel_type){
        return view('admin.fertilizer.fertilizationPredictPopup',compact('barrel_type'));
    }
}