<?php
namespace Gis\Models\Services;

use Gis\Models\Entities\ColorDefinitions;
use Gis\Models\Entities\FertilityMap;
use Gis\Models\Entities\FertilityMapSelection;
use Gis\Models\Entities\FertilizerMap;
use Gis\Models\Entities\FertilizerMapInfo;
use Gis\Models\Entities\FertilizerMapPayment;
use Gis\Models\Entities\FertilizerMapProperty;
use Gis\Models\Entities\FolderLayer;
use Gis\Models\Entities\StandardUser;
use Gis\Models\Entities\StandardCrop;
use Gis\Models\Entities\StandardCropNito;
use Gis\Models\Entities\StandardCropPhotpho;
use Gis\Models\Entities\StandardCropKali;
use Gis\Models\MapTools;
use Gis\Models\Repositories\FertilityMapSelectionFacade;
use Gis\Models\Repositories\FertilityMapSelectionInfoFacade;
use Gis\Models\Repositories\FertilizerFacade;
use Gis\Models\Repositories\FertilizerMapFacade;
use Gis\Models\Repositories\FertilizerMapInfoFacade;
use Gis\Models\Repositories\FertilizerMapPaymentFacade;
use Gis\Models\Repositories\FertilizerMapPropertyFacade;
use Gis\Models\Repositories\FolderFacade;
use Gis\Models\Repositories\SystemFertilizerDefinitionDetailNitoFacade;
use Gis\Models\Repositories\SystemFertilizerDefinitionDetailPhotphoFacade;
use Gis\Models\Repositories\SystemFertilizerDefinitionDetailKaliFacade;
use Gis\Models\Repositories\UserFacade;
use Gis\Models\Repositories\StandardUserFacade;
use Gis\Models\Repositories\StandardCropFacade;
use Gis\Models\Repositories\CropFacade;
use Gis\Models\Repositories\StandardCropNitoFacade;
use Gis\Models\Repositories\StandardCropPhotphoFacade;
use Gis\Models\Repositories\StandardCropKaliFacade;
use Gis\Models\Repositories\GroupFacade;
use Gis\Models\Repositories\MapColorFacade;
use Gis\Models\Repositories\FertilizationPriceFacade;
use Gis\Models\SystemCode;
use Illuminate\Pagination\LengthAwarePaginator;
use Gis\Exceptions\GisException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Gis\Helpers\LoggingAction;
use Gis\Services\Logging\ApplicationLogFacade;

/**
 * Methods to work with repositories.
 * Class FertilizerService
 *
 * @package Gis\Models\Services
 */
class FertilizerService extends BaseService implements FertilizerServiceInterface
{

    const MAP_EXIST = - 2;

    /**
     * Find Fertilizer standard By Id
     *
     * @param
     *            $id
     * @return
     *
     * @throws GisException
     */
    public function getById($id)
    {
        $fertilizer = FertilizerFacade::findByField('id', $id)->first();
        if (empty($fertilizer))
            throw new GisException(trans('common.fertilizer_standard_definiton_not_found'), SystemCode::NOT_FOUND);
        return $fertilizer;
    }

    /**
     * Get Fertilizer by Layer id
     * @param $layerId
     * @return mixed
     */
    public function getFertilizerByLayerId($layerId)
    {
        return FertilizerMapFacade::findByLayerId($layerId);
    }

    /**
     * Get Crops by id
     *
     * @param $id id
     *            of crops
     * @return the information of crops
     */
    public function getStandardCropById($id)
    {
        $result = StandardCropFacade::findByField('id', $id)->first();
        return $result;
    }

    /**
     * Get fertilizer map info
     * @param $postData
     * @param $paggingRequest
     * @return mixed
     */
    public function getFertilizers($postData, $paggingRequest)
    {
        $user = session('user');

        $specifies = StandardUserFacade::selectModel()->where('user_code', '=', $user->user_code)->get();

        $specifyIds = array();
        foreach ($specifies as $specify) {
            array_push($specifyIds, $specify->fertilizer_standard_definition_id);
        }

        $isAdmin = $user->usergroup->auth_authorization;
        $limit = $paggingRequest['rows'];

        if ($isAdmin) {
            $rows = FertilizerFacade::selectModel()->orderBy('fertilization_standard_name', 'asc')->paginate($limit);

            $result = $this->buildResponser($rows, $paggingRequest['page']);
            return response()->json($result);
        }

        $rows = FertilizerFacade::selectModel()->where('ins_user', '=', $user->user_code)
            ->orWhere('created_by', '0')
            ->
            // created_by: 0-public; 1-admin; 2-user
            orWhereIn('id', $specifyIds)
            ->orderBy('fertilization_standard_name', 'asc')
            ->paginate($limit);

        $result = $this->buildResponser($rows, $paggingRequest['page']);
        return response()->json($result);
    }

    /**
     * Clear System fertilization of one crop
     * @param $fertilizerStandardId
     * @param $standardCropId
     * @param $paggingRequest
     */
    public function clearSystemStandardCropDetails($fertilizerStandardId, $standardCropId, $paggingRequest){
        //verify to make sure the current crop is not in use
        //otherwise throw exception
        //we just need to look for if any item in table fertilizer_map_properties
        //that has one record with fertilizer_standard_definition_id & crops_id
        $itemInUse = FertilizerMapPropertyFacade::selectModel()
            ->where('fertilizer_standard_definition_id', '=', $fertilizerStandardId)
            ->where('crops_id', '=', $standardCropId)->get();
        if(!is_null($itemInUse) && count($itemInUse)>0){
            throw new GisException(trans('common.fertilizer_system_cannot_clear_crop'));
        }
        else{
            //process to delete
            DB::transaction(function () use ($fertilizerStandardId, $standardCropId) {
                SystemFertilizerDefinitionDetailNitoFacade::selectModel()
                    ->where('fertilizer_standard_definition_id',$fertilizerStandardId)
                    ->where('crops_id',$standardCropId)
                    ->delete();
                SystemFertilizerDefinitionDetailPhotphoFacade::selectModel()
                    ->where('fertilizer_standard_definition_id',$fertilizerStandardId)
                    ->where('crops_id',$standardCropId)
                    ->delete();
                SystemFertilizerDefinitionDetailKaliFacade::selectModel()
                    ->where('fertilizer_standard_definition_id',$fertilizerStandardId)
                    ->where('crops_id',$standardCropId)
                    ->delete();
            });
            //and now return the data to end-user
            return $this->getSystemStandardCropDetails($fertilizerStandardId, $standardCropId, $paggingRequest);
        }
    }

    /**
     * get System Standard Crops detail
     *
     * @param
     *            $fertilizerStandardId
     * @param
     *            $standardCropId
     * @param
     *            $paggingRequest
     * @return array
     */
    public function getSystemStandardCropDetails($fertilizerStandardId, $standardCropId, $paggingRequest)
    {
        $nitoLists = SystemFertilizerDefinitionDetailNitoFacade::selectModel()->where('fertilizer_standard_definition_id', '=', $fertilizerStandardId)
            ->where('crops_id', '=', $standardCropId)
            ->orderBy('n', 'asc');

        $nito = $nitoLists->get();
        foreach ($nito as &$item) {
            $item->new = 0;
        }
        $existN = $nitoLists->lists('n')->all();
        for ($i = 1; $i <= 11; $i ++) {
            if (! in_array($i, $existN)) {
                $temp = new \stdClass();
                {
                    $temp->n_amount = null;
                    $temp->ratio = null;
                    $temp->division_amount1 = null;
                    $temp->division_amount2 = null;
                    $temp->division_amount3 = null;
                    $temp->division_amount4 = null;
                    $temp->division_amount5 = null;
                    $temp->division_amount6 = null;
                    $temp->division_amount7 = null;
                    $temp->division_amount8 = null;
                    $temp->division_amount9 = null;
                    $temp->division_amount10 = null;
                    $temp->division_amount11 = null;
                    $temp->division_amount12 = null;
                    $temp->division_amount13 = null;
                    $temp->division_amount14 = null;
                    $temp->division_amount15 = null;
                    $temp->division_amount16 = null;
                    $temp->division_amount17 = null;
                    $temp->division_amount18 = null;
                    $temp->division_amount19 = null;
                    $temp->division_amount20 = null;
                }
                $temp->n = $i;
                $temp->id = $i;
                $temp->new = 1;
                $nito->push($temp);
            }
        }
        $photphoList = SystemFertilizerDefinitionDetailPhotphoFacade::selectModel()->where('fertilizer_standard_definition_id', '=', $fertilizerStandardId)
            ->where('crops_id', '=', $standardCropId)
            ->orderBy('p', 'asc');
        $photpho = $photphoList->get();
        $kaliList = SystemFertilizerDefinitionDetailKaliFacade::selectModel()->where('fertilizer_standard_definition_id', '=', $fertilizerStandardId)
            ->where('crops_id', '=', $standardCropId)
            ->orderBy('k', 'asc');
        $kali = $kaliList->get();
        $result = array(
            'nito' => $nito,
            'photpho' => $photpho,
            'kali' => $kali
        );
        return $result;
    }

    /**
     *Get the fertilization standard of each crop
     * @param
     *            $standardCropId
     * @param
     *            $paggingRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStandardCropDetails($standardCropId, $paggingRequest)
    {
        $rows = array();
        // List of Nito
        $nitoList = StandardCropNitoFacade::selectModel()->where('user_fertilizer_definition_detail_id', '=', $standardCropId)
            ->orderBy('nitrogen', 'asc')
            ->get();
        $maxLength = sizeof($nitoList);

        // List of Photpho
        $photphoList = StandardCropPhotphoFacade::selectModel()->where('user_fertilizer_definition_detail_id', '=', $standardCropId)
            ->orderBy('p', 'asc')
            ->get();
        if ($maxLength < sizeof($photphoList))
            $maxLength = sizeof($photphoList);

        // List of Kali
        $kaliList = StandardCropKaliFacade::selectModel()->where('user_fertilizer_definition_detail_id', '=', $standardCropId)
            ->orderBy('k', 'asc')
            ->get();
        if ($maxLength < sizeof($kaliList))
            $maxLength = sizeof($kaliList);
        for ($i = 0; $i < $maxLength; $i ++) {
            $detailObj = null;

            if ($i < sizeof($nitoList)) {
                $detailObj['nito_extraction'] = $nitoList[$i]->nitrogen == sizeof($nitoList) ? $nitoList[$i]->nitrogen . trans('common.standardcropdetail_nito_total') : $nitoList[$i]->nitrogen;
                $detailObj['nito_amount'] = $nitoList[$i]->fertilization_standard_amount;
                $detailObj['nito_is_changed'] = $nitoList[$i]->is_changed;
            }

            if ($i < sizeof($photphoList)) {
                $detailObj['photpho_extraction'] = $photphoList[$i]->p;
                $detailObj['photpho_amount'] = $photphoList[$i]->fertilization_standard_amount;
                $detailObj['photpho_is_changed'] = $photphoList[$i]->is_changed;
            }

            if ($i < sizeof($kaliList)) {
                $detailObj['kali_extraction'] = $kaliList[$i]->k;
                $detailObj['kali_amount'] = $kaliList[$i]->fertilization_standard_amount;
                $detailObj['kali_is_changed'] = $kaliList[$i]->is_changed;
            }

            array_push($rows, $detailObj);
        }

        $result = array(
            'rows' => $rows
        );
        return response()->json($result);
    }

    /**
     * Build the response to user
     * @param LengthAwarePaginator $dataPagging
     * @param $currentPage
     * @return array
     */
    public function buildResponserStandardCropDetails(LengthAwarePaginator $dataPagging, $currentPage)
    {
        $results = array();
        foreach ($dataPagging as $obj) {
            array_push($results, $obj);
        }

        $response = array(
            'page' => ($dataPagging->isEmpty()) ? $dataPagging->currentPage() : $currentPage,
            'total' => ($dataPagging->isEmpty()) ? 1 : $dataPagging->lastPage(),
            'records' => ($dataPagging->isEmpty()) ? 0 : $dataPagging->total(),
            'rows' => $results
        );

        return $response;
    }

    /**
     * Get all the users that're specified to a fertilization standard
     * @param $postData
     * @param $paggingRequest
     * @return aray
     */
    public function getSpecifyUsers($postData, $paggingRequest)
    {
        $list = UserFacade::getSpecifyUsers($paggingRequest['rows'], 'id', 'asc');

        $response = $this->buildResponser($list, $paggingRequest['page']);
        return $response;
    }

    /**
     * Delete a fertilizer standard
     * @param array $ids
     * @return mixed
     * @throws GisException
     */
    public function deleteFertilizers(array $ids)
    {
        $fertilizerMap=FertilizerMapPropertyFacade::selectModel()->whereIn('fertilizer_standard_definition_id',$ids)->get();
        if(count($fertilizerMap)!=0){
            throw new GisException(trans('common.fertilizer_can_not_delete_fertilizer_in_map'));
        }
        // Check fertilizer created by administrator
        $adminFertilizers = FertilizerFacade::selectModel()->whereIn('id', $ids)
            ->where('created_by', '=', '0')
            ->get();
        if ((! session('user')->usergroup->auth_authorization) && (count($adminFertilizers)!=0)) {
            throw new GisException(trans('common.fertilizer_can_not_delete_admin'));
        }
        if(count($adminFertilizers)>0) {
            DB::transaction(function () use ($ids, $adminFertilizers) {
                foreach ($adminFertilizers as $adminFertilizer) {
                    if ($adminFertilizer->basis_of_calculation==true) {
                        throw new GisException(trans('common.fertilizer_can_not_delete_basic'));
                    }
                    $systemStandardNitos= $adminFertilizer->systemStandardNitos;
                    $systemStandardPhotphos= $adminFertilizer->systemStandardPhotphos;
                    $systemStandardKalis= $adminFertilizer->systemStandardKalis;
                    if($systemStandardNitos) {
                        foreach ($systemStandardNitos as $systemStandardNito) {
                            $systemStandardNito->delete();
                        }
                    }
                    if($systemStandardPhotphos) {
                        foreach ($systemStandardPhotphos as $systemStandardPhotpho) {
                            $systemStandardPhotpho->delete();
                        }
                    }
                    if($systemStandardKalis) {
                        foreach ($systemStandardKalis as $systemStandardKali) {
                            $systemStandardKali->delete();
                        }
                    }
                }
            });
        }
        // StandardCrop
        $standardCropList = StandardCropFacade::selectModel()->whereIn('fertilizer_standard_definition_id', $ids)
            ->orderBy('id', 'asc')
            ->get();

        $standardCropIds = array();
        foreach ($standardCropList as $standardCropObj) {
            array_push($standardCropIds, $standardCropObj->id);
        }
        DB::transaction(function () use ($ids, $standardCropIds) {
            StandardUserFacade::deleteByField($ids, 'fertilizer_standard_definition_id');

            StandardCropNitoFacade::deleteByField($standardCropIds, 'user_fertilizer_definition_detail_id');
            StandardCropPhotphoFacade::deleteByField($standardCropIds, 'user_fertilizer_definition_detail_id');
            StandardCropKaliFacade::deleteByField($standardCropIds, 'user_fertilizer_definition_detail_id');
            StandardCropFacade::deleteMany($standardCropIds);

            FertilizerFacade::deleteMany($ids);
        });

        // Add application log
        ApplicationLogFacade::logActionMode2(LoggingAction::MODE2_DELETE_FERTILIZATION_STANDARD, $ids);
        return response()->json(buildResponseMessage(trans('common.message_delete_success'), 1, null, null));
    }

    /**
     * Save fertilizer.
     *
     * @param array $postData
     * @return
     *
     * @throws GisException
     */
    public function saveFertilizer(array $postData)
    {
        if ($postData['agreedSave'] == 1) {
            $initial = empty($postData['initial_display']) ? '0' : '1';
            if ($initial == 1) {
                if (FertilizerFacade::deleteInitialBasicExist('initial_display')) {}
            }
            $basic = empty($postData['basis_of_calculation']) ? '0' : '1';
            if ($basic == 1) {
                if (FertilizerFacade::deleteInitialBasicExist('basis_of_calculation')) {}
            }
        } else {
            $initial = empty($postData['initial_display']) ? '0' : '1';
            if ($initial == 1) {
                $result = FertilizerFacade::checkInitialBasicExist('initial_display');
                if ($result != null)
                    if ($result->id != $postData['id'])
                        return response()->json(buildResponseMessage(trans('common.fertilizer_info_initial_display_exist'), SystemCode::CONFLICT, null, null));
            }
            $basic = empty($postData['basis_of_calculation']) ? '0' : '1';
            if ($basic == 1) {
                $result = FertilizerFacade::checkInitialBasicExist('basis_of_calculation');
                if ($result != null)
                    if ($result->id != $postData['id'])
                        return response()->json(buildResponseMessage(trans('common.fertilizer_info_basic_exist'), SystemCode::CONFLICT, null, null));
            }
        }
        // return $postData;
        $user = session("user");
        // createdBy: 0-puplic; 1-admin; 2-user
        $createdBy = 2;
        if ($user->usergroup->auth_authorization) {
            $createdBy = empty($postData['public']) ? '1' : '0';
        }

        // Check usergroup
        $group = GroupFacade::getById($user->user_group_id);
        if (! $group) {
            throw new GisException(trans('common.usergroup_id_not_existed'));
        }

        // Check fertilizer name
        $checkingFertilizer = FertilizerFacade::findByField('fertilization_standard_name', $postData['fertilization_standard_name'])->first();
        if ($checkingFertilizer != null && $checkingFertilizer->id != $postData['id']) {
            throw new GisException(trans('common.fertilizer_name_exists'),SystemCode::DB_ERROR);
        }

        if ($postData['id'] != null && $postData['id'] != '') {

            // Update fertilizer
            $this->editFertilizer($createdBy, $postData);
            return response()->json(buildResponseMessage(trans('common.fertilizer_info_save_success'), SystemCode::SUCCESS, null, null));
        }

        // Insert fertilizer
        $new_fertilizer=$this->addFertilizer($createdBy, $postData);
        if(!$user->usergroup->auth_authorization){
            $attribute=array(
                'fertilizer_standard_definition_id'=>$new_fertilizer->id,
                'user_code'=>$user->user_code,
            );
            $item= $this->modifyData($attribute,true);
            StandardUserFacade::create($item);
        }
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_CREATE_FERTILIZER_STANDARD,$postData);
        return response()->json(buildResponseMessage(trans('common.fertilizer_info_save_success'), 200, null, null));
    }

    /**
     * Open form for user to edit fertilization standard
     * @param $createdBy
     * @param array $postData
     * @throws GisException
     */
    private function editFertilizer($createdBy, array $postData)
    {
        $fertilizer = FertilizerFacade::findByField('id', $postData['id'])->first();
        if ($fertilizer == null) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }
        if (!session("user")->usergroup->auth_authorization)
        {
            if(!StandardUserFacade::selectModel()->where('fertilizer_standard_definition_id',$postData['id'])
                    ->where('user_code',session("user")->user_code)->first()
                && !FertilizerFacade::selectModel()->where('id',$postData['id'])
                    ->where('ins_user',session("user")->user_code)->first()
            )
                throw new GisException(trans('common.fertilizer_can_not_edit_auth'));
        }

        // Update fertilizer
        $attributes = array(
            'fertilization_standard_name' => $postData['fertilization_standard_name'],
            'range_of_application' => $postData['range_of_application'],
            'notes' => $postData['notes'],
            'remarks' => $postData['remarks'],
            'not_available' => empty($postData['not_available']) ? '0' : '1',
            'initial_display' => empty($postData['initial_display']) ? '0' : '1',
            'basis_of_calculation' => empty($postData['basis_of_calculation']) ? '0' : '1'
        );
        if(strlen($attributes['range_of_application'])>500||strlen($attributes['notes'])>300||strlen($attributes['remarks'])>300)
            throw new GisException(trans('common.fertilizer_can_not_save_overflow'),SystemCode::DB_ERROR);
        $attributes = $this->modifyData($attributes);
        FertilizerFacade::update($attributes, $postData['id']);
        //add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_UPDATE_FERTILIZER_STANDARD,$postData);
    }

    /**
     * Add fertilization standard
     *
     * @param
     *            $createdBy
     * @param array $postData
     */
    private function addFertilizer($createdBy, array $postData)
    {
        $attributes = array(
            'created_by' => $createdBy,
            'fertilization_standard_name' => $postData['fertilization_standard_name'],
            'range_of_application' => $postData['range_of_application'],
            'notes' => $postData['notes'],
            'remarks' => $postData['remarks'],
            'not_available' => empty($postData['not_available']) ? '0' : '1',
            'initial_display' => empty($postData['initial_display']) ? '0' : '1',
            'basis_of_calculation' => empty($postData['basis_of_calculation']) ? '0' : '1'
        );
        if(strlen($attributes['range_of_application'])>500||strlen($attributes['notes'])>300||strlen($attributes['remarks'])>300)
            throw new GisException(trans('common.fertilizer_can_not_save_overflow'),SystemCode::DB_ERROR);
        $attributes = $this->modifyData($attributes, true);

        $item=FertilizerFacade::create($attributes);
        return $item;
    }

    /**
     * Save StandardUser.
     *
     * @param array $postData
     * @return
     *
     * @throws GisException
     */
    public function saveStandardUser(array $postData)
    {
        $fertilizerId = $postData['fertilizer-id'];

        $oldSpecifyUsers = StandardUserFacade::findByField('fertilizer_standard_definition_id', $fertilizerId);
        $newUserCodes = explode(',', $postData['selected_ids']);

        $deleteIds = array();
        $oldUserCodes = array();
        for ($i = 0; $i < sizeof($oldSpecifyUsers); $i ++) {
            if (! in_array($oldSpecifyUsers[$i]->user_code, $newUserCodes)) {
                array_push($deleteIds, $oldSpecifyUsers[$i]->id);
            }
            array_push($oldUserCodes, $oldSpecifyUsers[$i]->user_code);
        }

        $fertilizer = FertilizerServiceFacade::getById($fertilizerId);
        if (! $fertilizer) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }

        $addUserCodes = array();
        $attributeList = array();
        for ($i = 0; $i < sizeof($newUserCodes); $i ++) {
            if (sizeof($newUserCodes) == 1 && $newUserCodes[0] == '') {
                break;
            }

            if (! in_array($newUserCodes[$i], $oldUserCodes)) {
                array_push($addUserCodes, $newUserCodes[$i]);

                // Insert new item to database
                $user = UserFacade::findByField('user_code', $newUserCodes[$i])->first();
                if ($user) {
                    $obj = new StandardUser();
                    $obj->user_code = $newUserCodes[$i];
                    $obj->fertilizer_standard_definition_id = $fertilizerId;
                    $obj = $this->modifyObject($obj, true);
                    array_push($attributeList, $obj);
                }
            }
        }

        DB::transaction(function () use($fertilizer, $deleteIds, $attributeList)
        {
            // Delete items not in the list
            StandardUserFacade::deleteMany($deleteIds);
            $fertilizer->standardUsers()->saveMany($attributeList);
            ApplicationLogFacade::logActionMode2(LoggingAction::MODE2_GRANT_ACCESS_RIGHT_FERTILIZATION_STANDARD, $attributeList);
        });

        return response()->json(buildResponseMessage(trans('common.fertilizer_info_save_success'), 1, null, null));
    }

    /**
     * Get Array StandardUsers.
     *
     * @param String $default
     *
     * @return array
     */
    public function getArrayStandardUserCodes($fertilizerId)
    {
        $standardUserCodes = StandardUserFacade::findByField('fertilizer_standard_definition_id', $fertilizerId)->pluck('user_code');
        return $standardUserCodes;
    }

    /**
     * Get Array Photphos.
     *
     * @param int $fertilizerId
     * @param int $cropsId
     * @param boolean $isSystemStandard
     * @param int $userStandardId
     *
     * @return array
     */
    public function getArrayPhotphos($fertilizerId, $cropsId, $isSystemStandard, $userStandardId = null)
    {
        if ($isSystemStandard)
            $records = SystemFertilizerDefinitionDetailPhotphoFacade::selectModel()->select('p', 'ratio', 'assessment')
                ->where('fertilizer_standard_definition_id', '=', $fertilizerId)
                ->where('crops_id', '=', $cropsId)
                ->orderBy('p', 'asc')
                ->get();
        else
            $records = StandardCropPhotphoFacade::selectModel()->select('p', 'ratio')
                ->where('user_fertilizer_definition_detail_id', '=', $userStandardId)
                ->orderBy('p', 'asc')
                ->get();
        $array[''] = trans('common.select_item_null');
        $i = 0;
        $plists[] = null;
        if ($records->isEmpty())
            return $array;

        foreach ($records as $obj) {
            $plists[$i] = $obj->p;
            $i ++;
        }

        foreach ($records as $obj) {
            if (empty($obj->ratio))
                continue;
            if ($isSystemStandard) {
                $key = array_search($obj->p, $plists);
                if (empty($plists[$key + 1]))
                    $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount_more') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                else
                    $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount') . $plists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
            } else {
                switch ($obj->p) {
                    case 0:
                        $key = array_search($obj->p, $plists);
                        $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount') . $plists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 5:
                        $key = array_search($obj->p, $plists);
                        $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount') . $plists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 10:
                        $key = array_search($obj->p, $plists);
                        $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount') . $plists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 15:
                        $key = array_search($obj->p, $plists);
                        $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount') . $plists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 30:
                        $key = array_search($obj->p, $plists);
                        $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount') . $plists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 45:
                        $key = array_search($obj->p, $plists);
                        $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount') . $plists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 60:
                        $key = array_search(45, $plists);
                        if ($key)
                            $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount_more') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        else
                            $array[$obj->ratio] = $obj->p . trans('common.soil_analysis_amount_more') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                }
            }
        }

        return $array;
    }

    /**
     * Get Fertilization standard for a crop
     * @param $fertilizerId
     * @param $cropId
     * @return mixed
     */
    public function getStandardCrop($fertilizerId, $cropId)
    {
        $obj = StandardCropFacade::selectModel()->where('fertilizer_standard_definition_id', '=', $fertilizerId)
            ->where('crops_id', '=', $cropId)
            ->first();
        return $obj;
    }

    /**
     * Get Array Kalis.
     *
     * @param int $fertilizerId
     * @param int $cropsId
     * @param boolean $isSystemStandard
     * @param int $userStandardId
     *
     * @return array
     */
    public function getArrayKalis($fertilizerId, $cropsId, $isSystemStandard, $userStandardId = null)
    {
        if ($isSystemStandard)
            $records = SystemFertilizerDefinitionDetailKaliFacade::selectModel()->select('k', 'ratio', 'assessment')
                ->where('fertilizer_standard_definition_id', '=', $fertilizerId)
                ->where('crops_id', '=', $cropsId)
                ->orderBy('k', 'asc')
                ->get();
        else
            $records = StandardCropKaliFacade::selectModel()->select('k', 'ratio')
                ->where('user_fertilizer_definition_detail_id', '=', $userStandardId)
                ->orderBy('k', 'asc')
                ->get();
        $array[''] = trans('common.select_item_null');
        $i = 0;
        $klists[] = null;
        if ($records->isEmpty())
            return $array;
        foreach ($records as $obj) {
            $klists[$i] = $obj->k;
            $i ++;
        }
        foreach ($records as $obj) {
            if (empty($obj->ratio))
                continue;

            if ($isSystemStandard) {
                $key = array_search($obj->k, $klists);
                if (empty($klists[$key + 1]))
                    $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount_more') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                else
                    $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount') . $klists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
            } else {
                switch ($obj->k) {
                    case 0:
                        $key = array_search($obj->k, $klists);
                        $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount') . $klists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 8:
                        $key = array_search($obj->k, $klists);
                        $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount') . $klists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 15:
                        $key = array_search($obj->k, $klists);
                        $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount') . $klists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 30:
                        $key = array_search($obj->k, $klists);
                        $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount') . $klists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 50:
                        $key = array_search($obj->k, $klists);
                        $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount') . $klists[$key + 1] . trans('common.soil_analysis_amount_next') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 60:
                        $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount_more') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                    case 70:
                        $array[$obj->ratio] = $obj->k . trans('common.soil_analysis_amount_more') . trans('common.soil_analysis_delimiter') . $obj->assessment;
                        break;
                }
            }
        }

        return $array;
    }

    /**
     * Get array crops.
     *
     * @param String $default
     *
     * @return array
     */
    public  function getArrayCrops($standardId, $cropId = null)
    {
        $standardCrops = StandardCropFacade::findByField('fertilizer_standard_definition_id', $standardId)->pluck('crops_id');
        $existedCrops = array();
        foreach ($standardCrops as $standardCrop) {
            array_push($existedCrops, $standardCrop);
        }

        if ($cropId != null) {
            $index = array_search($cropId, $existedCrops);
            unset($existedCrops[$index]);
        }

        $array[''] = trans('common.select_item_null');
        $crops = CropFacade::orderBy("order_number","ASC")->get();

        if ($crops != null) {
            foreach ($crops as $crop) {
                if (! in_array($crop->id, $existedCrops))
                    $array[$crop->id] = $crop->crops_name;
            }
        }
        return $array;
    }
    /**
     * Get array all crops for system standard crop.
     *
     * @param String $default
     *
     * @return array
     */
    public function getArrayAllCrops()
    {
        $existedCrops=array();
        $crops = CropFacade::orderBy("order_number","ASC")->get();

        if ($crops != null) {
            foreach ($crops as $crop) {
                if (! in_array($crop->id, $existedCrops))
                    $array[$crop->id] = $crop->crops_name;
            }
        }
        return $array;
    }

    /**
     * Get array crops exists in fertilizer system standard crop.
     *
     * @param String $default
     *
     * @return array
     */
    function getArrayCropExists($standardId, $cropId = null)
    {
        $standardCrops = SystemFertilizerDefinitionDetailNitoFacade::findByField('fertilizer_standard_definition_id', $standardId)->pluck('crops_id');
        $existedCrops = array();
        foreach ($standardCrops as $standardCrop) {
            array_push($existedCrops, $standardCrop);
        }

        if ($cropId != null) {
            $index = array_search($cropId, $existedCrops);
            unset($existedCrops[$index]);
        }

        $array[''] = trans('common.select_item_null');
        $crops = CropFacade::orderBy("order_number","ASC")->get();

        if ($crops != null) {
            foreach ($crops as $crop) {
                if (in_array($crop->id, $existedCrops))
                    $array[$crop->id] = $crop->crops_name;
            }
        }
        return $array;
    }

    /**
     * Get array normal fertilizer.
     *
     * @param String $default
     *
     * @return array
     */
    public function getArrayNormalFertilizer()
    {
        if(session('user')->usergroup->auth_authorization==1) {
            $fertilizers = FertilizerFacade::selectModel()->whereIn('created_by', [
                1,
                2
            ])->get();
        }
        else{
            $fertilizers1 = FertilizerFacade::selectModel()->whereIn('created_by', [
                1,
                2
            ])->join('fertilizer_standard_user_relations','fertilizer_standard_definitions.id','=','fertilizer_standard_user_relations.fertilizer_standard_definition_id')
                ->where('fertilizer_standard_user_relations.user_code',session('user')->user_code)
                ->select('fertilizer_standard_definitions.*')
                ->get()
            ;
            $fertilizers2 = FertilizerFacade::selectModel()->where('ins_user',session('user')->user_code)->get();
            $fertilizers= $fertilizers1->merge($fertilizers2);
        }
        $array[''] = trans('common.select_item_null');
        if ($fertilizers != null) {
            foreach ($fertilizers as $fertilizer)
                $array[$fertilizer->id] = $fertilizer->fertilization_standard_name;
        }
        return $array;
    }

    /**
     * Get array crops.
     *
     * @param String $default
     * @param boolean $isGuest
     *
     * @return array
     */
    public function getArrayFertilizerByCrop($cropId, $isGuest = null)
    {
        $fertilizerDefinitions = $this->getListFertilizerStandardIds($cropId);
        $array['data'][''] = trans('common.select_item_null');
        $array['initial'][''] = trans('common.select_item_null');
        foreach ($fertilizerDefinitions as $obj) {
            if ($isGuest) {
                if ($obj->created_by != 0)
                    continue;
            }

            $array['data'][$obj->id] = $obj->fertilization_standard_name;
            $array['initial'][$obj->id] = $obj->initial_display;

        }
        $fertilizerIds = array_keys($array['data']);
        $array['defaultId'] = empty($fertilizerIds[1]) ? $fertilizerIds[0] : $fertilizerIds[1];

        return $array;
    }

    /**
     * Get array standard fertilizer by crops with account admin.
     *
     * @param String $default
     * @param boolean $isGuest
     *
     * @return array
     */
    public function getArrayStandardFertilizerByCrop($cropId, $isGuest = null, $standardFertilizer)
    {
        $fertilizerDefinitions = $this->getListFertilizerStandardIds($cropId);
        $array['data'][''] = trans('common.select_item_null');
        $array['initial'][''] = trans('common.select_item_null');
        foreach ($fertilizerDefinitions as $obj) {
            if ($isGuest) {
                if ($obj->created_by != 0)
                    continue;
            }
            $array['data'][$obj->id] = $obj->fertilization_standard_name;
            $array['initial'][$obj->id] = $obj->initial_display;
        }
        $fertilizerIds = array_keys($array['data']);
        $array['defaultId'] = empty($fertilizerIds[1]) ? $fertilizerIds[0] : $fertilizerIds[1];

        return $array;
    }

    /**
     * Get List Ids Of Fertilizer definition standard detail by crop
     *
     * @param int $cropsId
     *
     * @return array() $fertilizerIds
     */
    public function getListFertilizerStandardIds($cropsId)
    {
        $fertilizerDefineIds = FertilizerFacade::getFertilizersByCropAndUserCode($cropsId);
        return $fertilizerDefineIds;
    }

    /**
     * Copy fertilizer.
     *
     * @param array $postData
     * @return
     *
     * @throws GisException
     */
    public  function copyFertilizer(array $postData)
    {
        $fertilizerId = $postData['hidden_fertilizer_id'];
        // return $postData;
        $user = session("user");
        $group = GroupFacade::getById($user->user_group_id);
        if (! $group) {
            throw new GisException(trans('common.usergroup_id_not_existed'));
        }

        $fertilizer = FertilizerFacade::findByField('id', $fertilizerId)->first();
        if (! $fertilizer) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }

        // Check standard name
        $standardName = $fertilizer->fertilization_standard_name . trans('common.extend_copy');

        $checkingFertilizer = FertilizerFacade::selectModel()->where('fertilization_standard_name', 'like', $standardName . '%')
            ->orderBy('id', 'desc')
            ->first();
        if ($checkingFertilizer) {
            $extend = substr($checkingFertilizer->fertilization_standard_name, strlen($standardName));
            $i = 1;
            while ($i <= $extend) {
                $i ++;
            }
            $standardName = $standardName . $i;
        }

        $newFertilizer = $fertilizer->replicate();
        unset($newFertilizer->id);
        $newFertilizer->created_by = $group->auth_authorization ? 1 : 2; // created_by: 0-public; 1-admin; 2-user;
        $newFertilizer->fertilization_standard_name = $standardName;
        $newFertilizer->initial_display=false;
        $newFertilizer = $this->modifyObject($newFertilizer, true);

        DB::transaction(function () use($fertilizer, $newFertilizer)
        {
            $newFertilizer->save();
            // II. Copy Normal Fertilizer Standard
            foreach ($fertilizer->standardCrops as $standardCropItem) {

                // 1.Copy StandardCropNito
                $nitos = $this->getStandardCropNito($standardCropItem);

                // 2.Copy StandardCropPhotpho
                $photphos = $this->getStandardCropPhotpho($standardCropItem);

                // 3.Copy StandardCropKali
                $kalis = $this->getStandardCropKali($standardCropItem);

                $newStandardCropObj = $standardCropItem->replicate();
                $newStandardCropObj->user_code = session('user')->user_code;
                $newStandardCropObj = $this->modifyObject($newStandardCropObj, true);
                $newStandardCropObj = $newFertilizer->standardCrops()->save($newStandardCropObj);

                $newStandardCropObj->nito()->saveMany($nitos);
                $newStandardCropObj->photpho()->saveMany($photphos);
                $newStandardCropObj->kali()->saveMany($kalis);
            }
            //Add application log
            ApplicationLogFacade::logAction(LoggingAction::MODE2_COPY_FERTILIZATION_STANDARD,$fertilizer);
        });
        return response()->json(buildResponseMessage(trans('common.fertilizer_copy_success'), 1, null, null));
    }

    /**
     * Copy data from System fertilizer to user-defined fertilizer
     *
     * @param array $postData
     */
    public function copyFromSystemFertilizer(array $postData)
    {
        // 1. get target user-defined fertilizer
        // by fertilizer definition id & crops id
        $userDefinedStandardCrop = $this->getUserDefinedStandardCrop($postData['fertilizer_id'], $postData['crops_id']);
        if($postData['overWrite']==0){
            $temp=StandardCropFacade::selectModel()->where('fertilizer_standard_definition_id',$postData['fertilizer_id'])->where('crops_id',$postData['crops_id'])->first();
            if($temp)
                return response()->json(buildResponseMessage(trans('common.fertilizer_standard_crops_exist'), SystemCode::CONFLICT, null, null));
        }
        DB::transaction(function () use($userDefinedStandardCrop, $postData)
        {
            if ($userDefinedStandardCrop != null) {
                $this->saveUserDefinedStandardCrop($postData, $userDefinedStandardCrop->id);
                // 2. get nito of system fertilizer
                StandardCropNitoFacade::selectModel()->where('user_fertilizer_definition_detail_id', $userDefinedStandardCrop->id)->delete();
                StandardCropPhotphoFacade::selectModel()->where('user_fertilizer_definition_detail_id', $userDefinedStandardCrop->id)->delete();
                StandardCropKaliFacade::selectModel()->where('user_fertilizer_definition_detail_id', $userDefinedStandardCrop->id)->delete();
            }
            else {
                $this->saveUserDefinedStandardCrop($postData, false);
            }
            $userDefinedStandardCropTarget = $this->getUserDefinedStandardCrop($postData['fertilizer_id'], $postData['crops_id']);
            // 2. get nito of system fertilizer
            $nitos = $this->getSystemStandardCropNitos($postData, $userDefinedStandardCropTarget->id);
            foreach ($nitos as $nito) {
                StandardCropNitoFacade::create($nito);
            }
            // 3. get photpho of system fertilizer
            $photphos = $this->getSystemStandardCropPhotphos($postData, $userDefinedStandardCropTarget->id);
            foreach ($photphos as $photpho) {
                StandardCropPhotphoFacade::create($photpho);
            }
            // 4. get kali of system fertilizer
            $kalis = $this->getSystemStandardCropKalis($postData, $userDefinedStandardCropTarget->id);
            foreach ($kalis as $kali) {
                StandardCropKaliFacade::create($kali);
            }
            //Add application log
            ApplicationLogFacade::logAction(LoggingAction::ACTION_COPY_SYSTEM_FERTILIZATION_STANDARD,$postData);
        });
        return response()->json(buildResponseMessage(trans('common.fertilizer_copy_success'), 1, null, null));
    }

    /**
     * Save user-defined standard crop
     * @param $postData
     * @param $id
     * @return bool
     */
    private function saveUserDefinedStandardCrop($postData, $id)
    {
        $newStandardUser = array(
            'fertilizer_standard_definition_id' => $postData['fertilizer_id'],
            'crops_id' => $postData['crops_id'],
            'not_available' => false,
            'fertilization_standard_amount_n' => $postData['inputN'],
            'fertilization_standard_amount_p' => $postData['inputP'],
            'fertilization_standard_amount_k' => $postData['inputK'],
            'user_code' => $postData['user_code']
        );
        if ($id == false) {
            $itemStandardUser = $this->modifyData($newStandardUser, true);
            StandardCropFacade::create($itemStandardUser);
        } else {
            $itemStandardUser = $this->modifyData($newStandardUser);
            StandardCropFacade::update($itemStandardUser, $id);
        }
        return true;
    }

    /**
     * Get user-defined standard crop
     * @param $fertilizerTarget
     * @param $cropId
     * @return mixed
     */
    private function getUserDefinedStandardCrop($fertilizerTarget, $cropId)
    {
        $targetUserDefinedFertilizer = StandardCropFacade::selectModel()->where('fertilizer_standard_definition_id', $fertilizerTarget)
            ->where('crops_id', $cropId)
            ->first();
        return $targetUserDefinedFertilizer;
    }

    /**
     * Get nitrogen system fertilization
     * @param $postData
     * @param $userDefinedStandardCropTargetId
     * @return array
     */
    private function getSystemStandardCropNitos($postData, $userDefinedStandardCropTargetId)
    {
        $arrayNito = array();
        $standardCropNitos = SystemFertilizerDefinitionDetailNitoFacade::selectModel()->where('fertilizer_standard_definition_id', $postData['fertilizer_get'])
            ->where('crops_id', $postData['crops_id'])
            ->get();
        foreach ($standardCropNitos as $standardCropNito) {
            $item = array(
                'user_fertilizer_definition_detail_id' => $userDefinedStandardCropTargetId,
                'ratio' => $standardCropNito->ratio,
                'nitrogen' => $standardCropNito->n,
                'is_changed' => false,
                'fertilization_standard_amount' => (int) ($postData['inputN'] * $standardCropNito->ratio)
            );
            array_push($arrayNito, $this->modifyData($item, true));
        }
        return $arrayNito;
    }

    /**
     * Get photpho system fertilization
     * @param $postData
     * @param $userDefinedStandardCropTargetId
     * @return array
     */
    private function getSystemStandardCropPhotphos($postData, $userDefinedStandardCropTargetId)
    {
        $arrayPhotpho = array();
        $standardCropPhotphos = SystemFertilizerDefinitionDetailPhotphoFacade::selectModel()->where('fertilizer_standard_definition_id', $postData['fertilizer_get'])
            ->where('crops_id', $postData['crops_id'])
            ->get();
        foreach ($standardCropPhotphos as $standardCropPhotpho) {
            $item = array(
                'user_fertilizer_definition_detail_id' => $userDefinedStandardCropTargetId,
                'ratio' => $standardCropPhotpho->ratio,
                'p' => $standardCropPhotpho->p,
                'is_changed' => false,
                'fertilization_standard_amount' => (int) ($postData['inputP'] * $standardCropPhotpho->ratio)
            );
            array_push($arrayPhotpho, $this->modifyData($item, true));
        }
        return $arrayPhotpho;
    }

    /**
     * Get kali system fertilization
     * @param $postData
     * @param $userDefinedStandardCropTargetId
     * @return array
     */
    private function getSystemStandardCropKalis($postData, $userDefinedStandardCropTargetId)
    {
        $arrayKali = array();
        $standardCropKalis = SystemFertilizerDefinitionDetailKaliFacade::selectModel()->where('fertilizer_standard_definition_id', $postData['fertilizer_get'])
            ->where('crops_id', $postData['crops_id'])
            ->get();
        foreach ($standardCropKalis as $standardCropKali) {
            $item = array(
                'user_fertilizer_definition_detail_id' => $userDefinedStandardCropTargetId,
                'ratio' => $standardCropKali->ratio,
                'k' => $standardCropKali->k,
                'is_changed' => false,
                'fertilization_standard_amount' => (int) ($postData['inputK'] * $standardCropKali->ratio)
            );
            array_push($arrayKali, $this->modifyData($item, true));
        }
        return $arrayKali;
    }

    /**
     * Get nitrogen of user-defined fertilization
     * @param $standardCrop
     * @return array
     */
    private function getStandardCropNito($standardCrop)
    {
        $array = array();
        foreach ($standardCrop->nito as $item) {
            $obj = $item->replicate();
            $obj = $this->modifyObject($obj, true);
            array_push($array, $obj);
        }
        return $array;
    }

    /**
     * Get photpho of user-defined fertilization
     * @param $standardCrop
     * @return array
     */
    private function getStandardCropPhotpho($standardCrop)
    {
        $array = array();
        foreach ($standardCrop->photpho as $item) {
            $obj = $item->replicate();
            $obj = $this->modifyObject($obj, true);
            array_push($array, $obj);
        }
        return $array;
    }

    /**
     * Get kali of user-defined fertilization
     * @param $standardCrop
     * @return array
     */
    private function getStandardCropKali($standardCrop)
    {
        $array = array();
        foreach ($standardCrop->kali as $item) {
            $obj = $item->replicate();
            $obj = $this->modifyObject($obj, true);
            array_push($array, $obj);
        }
        return $array;
    }

    /**
     * Update,create System standard crop details
     *
     * @param array $postData
     * @return \Illuminate\Http\JsonResponse
     */
    function saveSystemStandardCropDetails(array $postData)
    {
        $fertilizerStandardId = $postData['hidden-standard-id'];
        $standardCropId = $postData['crops_id'];
        DB::transaction(function () use($postData, $fertilizerStandardId, $standardCropId)
        {
            $this->saveSystemStandardCropDetailNito($postData['dataChangeN'], $fertilizerStandardId, $standardCropId);
            $this->saveSystemStandardCropDetailPhotpho($postData['dataChangeP'], $postData['del-p-arr'], $fertilizerStandardId, $standardCropId);
            $this->saveSystemStandardCropDetailKali($postData['dataChangeK'], $postData['del-k-arr'], $fertilizerStandardId, $standardCropId);
            //Add application log
            ApplicationLogFacade::logAction(LoggingAction::MODE2_UPDATE_FERTILIZATION_DETAIL,$postData);
        });
        return response()->json(buildResponseMessage(trans('common.save_success'), SystemCode::SUCCESS, null, null));
    }

    /**
     * Update,Create system standard Crop Detail Nito
     *
     * @param
     *            $dataChangeN
     * @param
     *            $fertilizerStandardId
     * @param
     *            $standardCropId
     * @return true without error
     */
    private function saveSystemStandardCropDetailNito($dataChangeN, $fertilizerStandardId, $standardCropId)
    {
        $standardCropDetailNitos = json_decode($dataChangeN, true);
        foreach ($standardCropDetailNitos as $standardCropDetailNito) {
            if ($standardCropDetailNito['new'] === "1") {
                $item = $this->modifyData($standardCropDetailNito, true);
                unset($item['id']);
                foreach ($item as &$value) {
                    if ($value=="") {
                        $value = null;
                    }
                }
                $item['fertilizer_standard_definition_id'] = $fertilizerStandardId;
                $item['crops_id'] = $standardCropId;
                SystemFertilizerDefinitionDetailNitoFacade::create($item);
            } else {
                $item = $this->modifyData($standardCropDetailNito);
                foreach ($item as &$value) {
                    if ($value=="") {
                        $value = null;
                    }
                }
                SystemFertilizerDefinitionDetailNitoFacade::update($item, $standardCropDetailNito['id']);
            }
        }
        return true;
    }

    /**
     * Update,Create system standard Crop Detail Photpho
     *
     * @param
     *            $dataChangeP
     * @param
     *            $delArray
     * @param
     *            $fertilizerStandardId
     * @param
     *            $standardCropId
     * @return true without error
     */
    private function saveSystemStandardCropDetailPhotpho($dataChangeP, $delArray, $fertilizerStandardId, $standardCropId)
    {
        $delPhotphos = json_decode($delArray, true);
        if (! empty($delPhotphos)) {
            foreach ($delPhotphos as $delPhotpho) {
                if (strpos($delPhotpho, 'jqg') !== false||$delPhotpho=="")
                    continue;
                $delP = SystemFertilizerDefinitionDetailPhotphoFacade::find($delPhotpho);
                $delP->delete();
            }
        }
        $standardCropDetailPhotphos = json_decode($dataChangeP, true);
        foreach ($standardCropDetailPhotphos as $standardCropDetailPhotpho) {
            if (strpos($standardCropDetailPhotpho['id'], 'jqg') !== false) {
                $item = $this->modifyData($standardCropDetailPhotpho, true);
                unset($item['id']);
                foreach ($item as $key => &$value) {
                    if ($key == 'fertilization_standard_amount')
                        if ($value=="") {
                            $value = null;
                        }
                }
                $item['fertilizer_standard_definition_id'] = $fertilizerStandardId;
                $item['crops_id'] = $standardCropId;
                SystemFertilizerDefinitionDetailPhotphoFacade::create($item);
            } else {
                $item = $this->modifyData($standardCropDetailPhotpho);
                foreach ($item as $key => &$value) {
                    if ($key == 'fertilization_standard_amount')
                        if ($value=="") {
                            $value = null;
                        }
                }
                SystemFertilizerDefinitionDetailPhotphoFacade::update($item, $standardCropDetailPhotpho['id']);
            }
        }
        return true;
    }

    /**
     * Update,Create system standard Crop Detail Kali
     *
     * @param
     *            $dataChangeK
     * @param
     *            $delArray
     * @param
     *            $fertilizerStandardId
     * @param
     *            $standardCropId
     * @return true without error
     */
    private function saveSystemStandardCropDetailKali($dataChangeK, $delArray, $fertilizerStandardId, $standardCropId)
    {
        $delKalis = json_decode($delArray, true);
        if (! empty($delKalis)) {
            foreach ($delKalis as $delKali) {
                if (strpos($delKali, 'jqg') !== false||$delKali=="")
                    continue;
                SystemFertilizerDefinitionDetailKaliFacade::delete($delKali);
            }
        }
        $standardCropDetailKalis = json_decode($dataChangeK, true);
        foreach ($standardCropDetailKalis as $standardCropDetailKali) {
            if (strpos($standardCropDetailKali['id'], 'jqg') !== false) {
                $item = $this->modifyData($standardCropDetailKali, true);
                unset($item['id']);
                foreach ($item as $key => &$value) {
                    if ($key == 'fertilization_standard_amount')
                        if ($value=="") {
                            $value = null;
                        }
                }
                $item['fertilizer_standard_definition_id'] = $fertilizerStandardId;
                $item['crops_id'] = $standardCropId;
                SystemFertilizerDefinitionDetailKaliFacade::create($item);
            } else {
                $item = $this->modifyData($standardCropDetailKali);
                foreach ($item as $key => &$value) {
                    if ($key == 'fertilization_standard_amount')
                        if ($value=="") {
                            $value = null;
                        }
                }
                SystemFertilizerDefinitionDetailKaliFacade::update($item, $standardCropDetailKali['id']);
            }
        }
        return true;
    }

    /**
     * Save StandardCrop.
     *
     * @param array $postData
     * @return
     *
     * @throws GisException
     */
    public function saveStandardCrop(array $postData)
    {
        $crop = CropFacade::getById($postData['crops_id']);
        if (! $crop) {
            throw new GisException(trans('common.standardcrop_crop_not_exists'));
        }
        $fertilizerSystemBasic=FertilizerFacade::selectModel()->where('basis_of_calculation',true)->first();
        if (! $fertilizerSystemBasic) {
            throw new GisException(trans('common.system_basic_standard_not_exists'));
        }
        $postData['fertilizer_get']=$fertilizerSystemBasic->id;
        if ($postData['id'] != null && $postData['id'] != '') {
            // Update fertilizer
            $this->editStandardCrop($postData);
            return response()->json(buildResponseMessage(trans('common.fertilizer_info_save_success'), 1, null, null));
        }

        // Insert standard crop
        $this->addStandardCrop( $postData);
        //Add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_ADD_CROPS_FOR_FERTILIZATION_STANDARD,$postData);
        return response()->json(buildResponseMessage(trans('common.fertilizer_info_save_success'), 1, null, null));
    }

    /**
     * Open form for user to edit crop fertilization standard
     * @param array $postData
     * @throws GisException
     */
    private function editStandardCrop(array $postData)
    {
        $standardCrop = StandardCropFacade::findByField('id', $postData['id'])->first();
        if (! $standardCrop) {
            throw new GisException(trans('common.standardcrop_not_exists'));
        }

        // Update standard crop
        $attributes = array(
            'crops_id' => $postData['crops_id'],
            'remarks' => $postData['remarks'],
            'not_available' => empty($postData['not_available']) ? '0' : '1',
            'fertilization_standard_amount_n' => $postData['fertilization_standard_amount_n'],
            'fertilization_standard_amount_p' => $postData['fertilization_standard_amount_p'],
            'fertilization_standard_amount_k' => $postData['fertilization_standard_amount_k'],
            'user_code' => session('user')->user_code
        );

        $attributes = $this->modifyData($attributes);
        StandardCropFacade::update($attributes, $postData['id']);
        DB::transaction(function () use($standardCrop,$postData)
        {
            $postData['inputN']=$postData['fertilization_standard_amount_n'];
            $postData['inputP']=$postData['fertilization_standard_amount_p'];
            $postData['inputK']=$postData['fertilization_standard_amount_k'];
            StandardCropNitoFacade::selectModel()->where('user_fertilizer_definition_detail_id',$standardCrop->id)->delete();
            StandardCropPhotphoFacade::selectModel()->where('user_fertilizer_definition_detail_id',$standardCrop->id)->delete();
            StandardCropKaliFacade::selectModel()->where('user_fertilizer_definition_detail_id',$standardCrop->id)->delete();
            $arrayN = $this->getSystemStandardCropNitos($postData,$standardCrop->id);
            if(count($arrayN)==0){
                throw new GisException(trans('common.standardcrop_not_exists'));
            }
            foreach ($arrayN as $nito) {
                StandardCropNitoFacade::create($nito);
            }
            $arrayP = $this->getSystemStandardCropPhotphos($postData,$standardCrop->id);
            foreach ($arrayP as $photpho) {
                StandardCropPhotphoFacade::create($photpho);
            }
            $arrayK = $this->getSystemStandardCropKalis($postData,$standardCrop->id);
            foreach ($arrayK as $kali) {
                StandardCropKaliFacade::create($kali);
            }
        });
        //Add application log
        ApplicationLogFacade::logAction(LoggingAction::MODE2_UPDATE_FERTILIZATION_STANDARD,$attributes);
    }

    /**
     * Add crop for fertilization standard
     * @param array $postData
     * @throws GisException
     */
    private function addStandardCrop( array $postData)
    {
        $standardCrop = StandardCropFacade::selectModel()->where('crops_id', '=', $postData['crops_id'])
            ->where('fertilizer_standard_definition_id', '=', $postData['fertilizer_standard_definition_id'])
            ->get()
            ->first();

        if ($standardCrop != null) {
            throw new GisException(trans('common.standardcrop_exists'));
        }
        $standardCropAttributes = array(
            'fertilizer_standard_definition_id' => $postData['fertilizer_standard_definition_id'],
            'crops_id' => $postData['crops_id'],
            'remarks' => $postData['remarks'],
            'not_available' => empty($postData['not_available']) ? '0' : '1',
            'fertilization_standard_amount_n' => $postData['fertilization_standard_amount_n'],
            'fertilization_standard_amount_p' => $postData['fertilization_standard_amount_p'],
            'fertilization_standard_amount_k' => $postData['fertilization_standard_amount_k'],
            'user_code' => session('user')->user_code
        );
        $standardCropAttributes = $this->modifyData($standardCropAttributes, true);
        DB::transaction(function () use($standardCropAttributes,$postData)
        {
            $standardCrop = StandardCropFacade::create($standardCropAttributes);
            $postData['inputN']=$postData['fertilization_standard_amount_n'];
            $postData['inputP']=$postData['fertilization_standard_amount_p'];
            $postData['inputK']=$postData['fertilization_standard_amount_k'];
            $arrayN = $this->getSystemStandardCropNitos($postData,$standardCrop->id);
            if(count($arrayN)==0){
                throw new GisException(trans('common.standardcrop_not_exists'));
            }
            foreach ($arrayN as $nito) {
                StandardCropNitoFacade::create($nito);
            }
            $arrayP = $this->getSystemStandardCropPhotphos($postData,$standardCrop->id);
            foreach ($arrayP as $photpho) {
                StandardCropPhotphoFacade::create($photpho);
            }
            $arrayK = $this->getSystemStandardCropKalis($postData,$standardCrop->id);
            foreach ($arrayK as $kali) {
                StandardCropKaliFacade::create($kali);
            }
        });
    }

    /**
     * Save StandardCropDetails.
     *
     * @param array $postData
     * @return
     *
     * @throws GisException
     */
    public function saveStandardCropDetails(array $postData)
    {
        $standardCropId = $postData['standard_crop_id'];

        $standardCrop = StandardCropFacade::findByField('id', $standardCropId)->first();
        if (! $standardCrop) {
            throw new GisException(trans('common.standard_crop_not_exists'));
        }
        $standardCropDetails = json_decode($postData['data']);

        // Remove some text
        $totalText = trans('common.standardcropdetail_nito_total');
        foreach ($standardCropDetails as $standardCropDetail) {
            $standardCropDetail->nito_extraction = str_replace($totalText, "", $standardCropDetail->nito_extraction);
        }

        $nitos = array();
        $photphos = array();
        $kalis = array();

        foreach ($standardCropDetails as $standardCropDetail) {

            // setting list nito
            $item = $this->getItem($standardCrop->nito, 'nitrogen', $standardCropDetail->nito_extraction);
            $item->is_changed = $standardCropDetail->nito_amount != $item->fertilization_standard_amount;
            $item->fertilization_standard_amount = $standardCropDetail->nito_amount;

            $item = $this->modifyObject($item, true);
            array_push($nitos, $item);

            // setting list photpho
            if ($standardCropDetail->photpho_extraction !== '') {
                $item = $this->getItem($standardCrop->photpho, 'p', $standardCropDetail->photpho_extraction);
                $item->is_changed = $standardCropDetail->photpho_amount != $item->fertilization_standard_amount;
                $item->fertilization_standard_amount = $standardCropDetail->photpho_amount;

                $item = $this->modifyObject($item, true);
                array_push($photphos, $item);
            }

            // setting list kali
            if ($standardCropDetail->kali_extraction !== '') {
                $item = $this->getItem($standardCrop->kali, 'k', $standardCropDetail->kali_extraction);
                $item->is_changed = $standardCropDetail->kali_amount != $item->fertilization_standard_amount;
                $item->fertilization_standard_amount = $standardCropDetail->kali_amount;

                $item = $this->modifyObject($item, true);
                array_push($kalis, $item);
            }
        }

        // Save data to database
        DB::transaction(function () use($standardCrop, $nitos, $photphos, $kalis)
        {
            $standardCrop->nito()->saveMany($nitos);
            $standardCrop->photpho()->saveMany($photphos);
            $standardCrop->kali()->saveMany($kalis);
        });
        //Add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_UPDATE_DETAIL_INFO_FOR_CROPS,$standardCrop);

        return response()->json(buildResponseMessage(trans('common.save_success'), 1, null, null));
    }

    private function getItem($array, $columName, $value)
    {
        foreach ($array as $item) {
            if ($item->$columName == $value) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Copy standard crop.
     *
     * @param array $postData
     * @return
     *
     * @throws GisException
     */
    public function copyStandardCrop($standardCropId, $distinationStandardId)
    {
        $distinationStandardId = $distinationStandardId == "" ? 0 : $distinationStandardId;
        $fertilizer = FertilizerFacade::findByField('id', $distinationStandardId)->first();
        if (! $fertilizer) {
            throw new GisException(trans('common.fertilizer_not_exists'));
        }

        $standardCrop = StandardCropFacade::findByField('id', $standardCropId)->first();
        if (! $standardCrop) {
            throw new GisException(trans('common.standard_crop_not_exists'));
        }

        $exitedStandardCrop = StandardCropFacade::getByCropIdAvaiAndNot($distinationStandardId, $standardCrop->crops_id); // ->first();
        if ($exitedStandardCrop) {
            throw new GisException(trans('common.standardcrop_crop_existed_in_standard'));
        }

        $fertilizerSystemBasic=FertilizerFacade::selectModel()->where('basis_of_calculation',true)->first();
        $newStandardCrop = $standardCrop->replicate();
        $newStandardCrop->fertilizer_standard_definition_id = $distinationStandardId;

        // 1.Copy StandardCropNito
        $nitos = $this->getStandardCropNito($standardCrop);

        // 2.Copy StandardCropPhotpho
        $photphos = $this->getStandardCropPhotpho($standardCrop);

        // 3.Copy StandardCropKali
        $kalis = $this->getStandardCropKali($standardCrop);

        DB::transaction(function () use($newStandardCrop, $nitos, $photphos, $kalis)
        {
            $newStandardCrop->save();

            $newStandardCrop->nito()->saveMany($nitos);
            $newStandardCrop->photpho()->saveMany($photphos);
            $newStandardCrop->kali()->saveMany($kalis);
        });
        //Add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_COPY_CROPS_INFORMATION_TO_ANOTHER_FERTILIZER_STANDARD,$standardCrop);
        return response()->json(buildResponseMessage(trans('common.copy_success'), 1, null, null));
    }

    /**
     * Find user by keyword
     *
     * @see \Gis\Models\Services\UserServiceInterface::findUserByKeyword()
     */
    public function findFertilizerByKeyword($keyword, $standardId)
    {
        $user = session('user');
        $isAdmin = $user->usergroup->auth_authorization;
        $limit = 10;

        if ($isAdmin) {
            $rows = FertilizerFacade::selectModel()->where('id', '!=', $standardId)
                ->where('fertilization_standard_name', 'like', '%' . $keyword . '%')
                ->whereIn('created_by',[1,2])
                ->orderBy('fertilization_standard_name', 'asc')
                ->paginate($limit);
        } else {
            $specifies = StandardUserFacade::selectModel()->where('user_code', '=', $user->user_code)->get();
            $specifyIds = array();
            foreach ($specifies as $specify) {
                array_push($specifyIds, $specify->fertilizer_standard_definition_id);
            }

            session(array(
                'specifyIds' => $specifyIds
            ));

            $rows = FertilizerFacade::selectModel()->where('id', '!=', $standardId)
                ->where(function ($query)
                {
                    $query->where('ins_user', '=', session('user')->user_code)
                        ->orWhere(function ($query1)
                        {
                            $query1->whereIn('id', session('specifyIds'))
                                ->where('created_by', '!=', 0);
                        });
                })
                ->where('fertilization_standard_name', 'like', '%' . $keyword . '%')
                ->orderBy('fertilization_standard_name', 'asc')
                ->paginate($limit);
        }

        $result = array();
        foreach ($rows as $fertilizer) {
            $result[$fertilizer->id] = $fertilizer->fertilization_standard_name;
        }

        return response()->json($result);
    }

    /**
     * Delete standard crops.
     *
     * @param array $postData
     * @return
     *
     * @throws GisException
     */
    public function deleteStandardCrops(array $ids)
    {
        $standardCrops=StandardCropFacade::selectModel()->whereIn('id',$ids)->get();
        foreach($standardCrops as $standardCrop) {
            $fertilizerMap = FertilizerMapPropertyFacade::selectModel()->where('fertilizer_standard_definition_id', $standardCrop->fertilizer_standard_definition_id)
                ->where('crops_id',$standardCrop->crops_id)->get();
            if (count($fertilizerMap) != 0) {
                throw new GisException(trans('common.fertilizer_can_not_delete_crop_in_map'));
            }
        }
        DB::transaction(function () use($ids)
        {
            StandardCropNitoFacade::deleteByField($ids, 'user_fertilizer_definition_detail_id');
            StandardCropPhotphoFacade::deleteByField($ids, 'user_fertilizer_definition_detail_id');
            StandardCropKaliFacade::deleteByField($ids, 'user_fertilizer_definition_detail_id');
            StandardCropFacade::deleteMany($ids);
        });
        //Add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_DELETE_CROPS_OF_FERTILIZER_STANDARD,$ids);
        return response()->json(buildResponseMessage(trans('common.message_delete_success'), 1, null, null));
    }

    /**
     * Get list of colors from fertilizer map
     * @param $fertilizerId
     * @return array
     * @throws GisException
     */
    public function getColorsList($fertilizerId)
    {
        $fertilizer = FertilizerMap::where('layer_id', $fertilizerId)->first();
        if (empty($fertilizer)) {
            return [];
        }
        $fertilizerMapInfo = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', '=', $fertilizer->id)
            ->orderBy('main_fertilizer', 'desc')
            ->get();
        $colors = [];
        if (! empty($fertilizerMapInfo)) {

            $currentColors = $this->extractColorFertilizerMap($fertilizerMapInfo);
            $colors = $this->getDefaultColorsList($currentColors);
        }
        return $colors;
    }

    /**
     * Get control methodology of fertilizer map
     * @param $fertilizerId
     * @return bool
     */
    public function getControlMethodology($fertilizerId)
    {
        $fertilizer = FertilizerMap::where('layer_id', $fertilizerId)->first();
        $fertilizerMapProperty = FertilizerMapProperty::where('fertilizer_map_id', $fertilizer->id)->first();
        return in_array($fertilizerMapProperty->control_methodology, FertilizerMapProperty::$methodology);
    }

    /**
     * get color of fertilizer map
     * @param $fertilizerId
     * @return array
     */
    public function getColorOfFertilizerMapInfo($fertilizerId)
    {
        $colorLists = [];
        $fertilizer = FertilizerMap::where('layer_id', $fertilizerId)->first();
        if (empty($fertilizer)) {
            return $colorLists;
        }
        $fertilizerMapInfo = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', '=', $fertilizer->id)
            ->orderBy('main_fertilizer', 'desc')
            ->get();

        $colorTmp = [];
        foreach ($fertilizerMapInfo as $key) {
            $hex = $this->setRGBtoHexList($key->r, $key->g, $key->b);
            if (! in_array($hex, $colorTmp)) {
                array_push($colorTmp, $hex);
                $colorLists[$key->id] = array(
                    $hex,
                    round($key->main_fertilizer),
                    round($key->sub_fertilizer)
                );
            }
        }
        return $colorLists;
    }

    /**
     * Extract color of fertilizer map
     * @param $fertilizerIterator
     * @return array
     */
    public function extractColorFertilizerMap($fertilizerIterator)
    {
        $listColor = [];
        foreach ($fertilizerIterator as $key) {
            $color = $this->setRGBtoHexList($key->r, $key->g, $key->b);

            if (! in_array($color, $listColor)) {
                array_push($listColor, $color);
            }
        }
        return $listColor;
    }

    /**
     * Convert RGB to Hex value
     * @param $r
     * @param $g
     * @param $b
     * @return string
     */
    public function setRGBtoHexList($r, $g, $b)
    {
        $r = $this->convertRGBtoHex($r);
        $g = $this->convertRGBtoHex($g);
        $b = $this->convertRGBtoHex($b);
        return $r . $g . $b;
    }

    /**
     * Get the current color of fertilizer map
     * @param $layerId
     * @return array
     * @throws GisException
     */
    public function getColorCurrentFertilizerMap($layerId){

        $fertilizer = FertilizerMap::where('layer_id', $layerId)->first();

        if(empty($fertilizer)){
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }
        $fertilizer_info =  FertilizerMapInfo::where('fertilizer_id', $fertilizer->id)
            ->orderBy('main_fertilizer', 'desc')
            ->get();
        if(empty($fertilizer_info)){
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }

        $colorLists = [];
        $tmp_sub_main = [];
        foreach ($fertilizer_info as $key) {
            $rgb = $key->r . ',' . $key->g . ',' . $key->b;
            if (! in_array($rgb, $tmp_sub_main)) {
                array_push($tmp_sub_main, $rgb);
                $hex = $this->setRGBtoHexList($key->r, $key->g, $key->b);
                $colorLists[$key->id] = array(
                    $hex,
                    round($key->main_fertilizer),
                    round($key->sub_fertilizer)
                );
            }
        }
        return  $colorLists;

    }

    /**
     * Get color for fertilizer map
     * @param $layerId
     * @param array $mapInfoId
     * @return array
     */
    public function getColorForFertilizerMap($layerId, $mapInfoId = [])
    {
        $fertilizer = FertilizerMap::where('layer_id', $layerId)->first();
        $fertilizer_info = DB::table('fertilizer_map_infos')->select([
            'id',
            'fertilizer_id',
            'main_fertilizer',
            'sub_fertilizer',
            'r',
            'g',
            'b'
        ])
            ->where('fertilizer_id', $fertilizer->id)
            ->whereIn('id', $mapInfoId)
            ->get();
        if (empty($fertilizer_info)) {
            return null;
        }
        $colorLists = [];
        $tmp_sub_main = [];
        foreach ($fertilizer_info as $key) {
            $rgb = $key->r . ',' . $key->g . ',' . $key->b;
            if (! in_array($rgb, $tmp_sub_main)) {
                array_push($tmp_sub_main, $rgb);
                $hex = $this->setRGBtoHexList($key->r, $key->g, $key->b);
                $colorLists[$key->id] = array(
                    $hex,
                    round($key->main_fertilizer),
                    round($key->sub_fertilizer)
                );
            }
        }

        return [
            'colorLists' => $colorLists,
            'mapInfoId' => $mapInfoId
        ];
    }

    /**
     * get unused color
     * @param $fertilizerId
     * @param array $colorIds
     * @return array
     */
    public function getUnusedColors($fertilizerId, array $colorIds)
    {
        $fertilizerMapInfo = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', '=', $fertilizerId)
            ->orderBy('main_fertilizer', 'desc')
            ->get();
        $existsCodes = array();
        $colors = array();
        for ($i = 0; $i < sizeof($fertilizerMapInfo); $i ++) {
            if (in_array($fertilizerMapInfo[$i]->id, $colorIds))
                continue;
            $code = $this->getDecHexCode($fertilizerMapInfo[$i]->r, $fertilizerMapInfo[$i]->g, $fertilizerMapInfo[$i]->b);
            array_push($existsCodes, $code);
        }

        $mapColors = MapColorFacade::selectModel()->where('fertilization_number_of_patterns', '=', 11)
            ->orderBy('fertilization_pattern', 'asc')
            ->get();

        for ($i = 0; $i < sizeof($mapColors); $i ++) {
            $code = $this->getDecHexCode($mapColors[$i]->r, $mapColors[$i]->g, $mapColors[$i]->b);
            if (! in_array($code, $existsCodes)) {
                $color = $this->getDecHexCode($mapColors[$i]->r, $mapColors[$i]->g, $mapColors[$i]->b);
                array_push($colors, $color);
            }
        }

        return $colors;
    }

    /**
     * Get default color
     * @param array $colorIds
     * @return mixed
     * @throws GisException
     */
    public function getEditingDefaultColor(array $colorIds)
    {
        $selectedColor = DB::table('fertilizer_map_infos')->whereIn('id', $colorIds)->first();
        if (empty($selectedColor)) {
            throw new GisException(trans('common.fertilizer_details_not_exists'));
        }

        $selectedColor->main_fertilizer = round($selectedColor->main_fertilizer);
        $selectedColor->sub_fertilizer = round($selectedColor->sub_fertilizer);
        if (sizeof($colorIds) > 1) {
            $selectedColor->main_fertilizer = "";
            $selectedColor->sub_fertilizer = "";
            /*
             * $selectedColor->r = 0;
             * $selectedColor->g = 0;
             * $selectedColor->b = 0;
             */
        }
        return $selectedColor;
    }

    /**
     * Get Hex code from RGG
     * @param $r
     * @param $g
     * @param $b
     * @return string
     */
    public function getDecHexCode($r, $g, $b)
    {
        $r1 = dechex($r);
        if ($r < 16)
            $r1 = '0' . $r1;

        $g1 = dechex($g);
        if ($g < 16)
            $g1 = '0' . $g1;

        $b1 = dechex($b);
        if ($b < 10)
            $b1 = '0' . $b1;

        $color = $r1 . $g1 . $b1;
        return $color;
    }

    /**
     * Get default color list
     * @param $fertilizerDetails
     * @return array
     * @throws GisException
     */
    public function getDefaultColorsList($fertilizerDetails)
    {
        $mapColors = ColorDefinitions::all();
        if (empty($mapColors)) {
            throw new GisException(trans('common.data_init_not_correct_fertilization_number_of_patterns_table'));
        }

        $colors = array();
        foreach ($mapColors as $key) {
            $hex = $this->setRGBtoHexList($key->r, $key->g, $key->b);
            if (! in_array($hex, $fertilizerDetails)) {

                array_push($colors, $hex);
            }
        }
        return $colors;
    }

    /**
     * Convert RGB to Hex code
     * @param $value
     * @return string
     */
    public function convertRGBtoHex($value)
    {
        $r = dechex($value);
        return ($value < 16) ? $r = '0' . $r : $r;
    }

    /**
     * Verify whether the item is bin folder or not
     * @param FertilizerMapInfo $item
     * @return bool
     */
    public function getIsBinParentFertilizerMap(FertilizerMapInfo $item)
    {
        if (empty($item)) {
            return true;
        }
        $fertilizerMap = FertilizerMap::find($item->fertilizer_id);
        $folderLayer = FolderLayer::find($fertilizerMap->layer_id);
        $parentLayer = FolderLayer::find($folderLayer->parent_folder);
        return $parentLayer->is_recyclebin;
    }

    /**
     * Verify whether the fertilizer map is inside bin folder or not
     * @param $fertilizer_id
     * @return bool
     * @throws GisException
     */
    public function getIsBinFertilizerMap($fertilizer_id)
    {
        if (empty($fertilizer_id)) {
            return true;
        }

        $folderLayer = FolderLayer::find($fertilizer_id);
        if (empty($folderLayer)) {
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }
        $parentLayer = FolderLayer::find($folderLayer->parent_folder);
        if (empty($folderLayer)) {
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }
        return $parentLayer->is_recyclebin;
    }

    /**
     * Get map inside folder
     * @param $id
     * @return mixed
     */
    public function getMapInFolder($id)
    {
        return FolderFacade::find($id);
    }

    /**
     * @param $postData
     * @return \Illuminate\Http\JsonResponse|null
     * @throws GisException
     * Step 1 : Valid color
     */
    public function validColorDetails($postData)
    {
        if (!empty($postData['update_colors'])) {
            return null;
        }
        list($r, $g, $b) = explode(',', $postData['ColorCode']);
        $currentSelect = FertilizerMapInfoFacade::selectModel()->find($postData['CurrentColors']);
        $subBarrel = empty($postData['IsOnebarrel']) ? $postData['sub_barrel'] : 0;
        $curentSubBarrel = empty($postData['IsOnebarrel']) ? $currentSelect->sub_fertilizer : 0;

        if ($this->getIsBinParentFertilizerMap($currentSelect)) {
            throw new GisException(trans('common.parent_fertilizer_map_is_bin'));
        }
        if (empty($currentSelect)) {
            throw new GisException(trans('common.fertility_maps_not_exists'));
        }

        $currentExistSelect = FertilizerMapInfoFacade::selectModel()
            ->where('fertilizer_id', $currentSelect->fertilizer_id)
            ->whereRaw('( ROUND(main_fertilizer) = '.$postData['main_barrel'].' and ROUND(sub_fertilizer) = '.$subBarrel.' )')
            ->whereRaw('( r = '.$r.' and g = '.$g.'  and b = '.$b.' )')
            ->where('id', $postData['CurrentColors'])->first();
        if ($currentExistSelect) {
            throw new GisException(trans('common.save_success'));
        }

        //check same value and color
        $valueSubMainQuery = FertilizerMapInfoFacade::selectModel()
            ->where('fertilizer_id', $currentSelect->fertilizer_id)
            ->whereRaw('( ROUND(main_fertilizer) = '.$postData['main_barrel'].' and ROUND(sub_fertilizer) = '.$subBarrel.' )')
            ->whereRaw('( r = '.$r.' and g = '.$g.'  and b = '.$b.' )')->first();
        if ($valueSubMainQuery) {
            return response()->json(buildResponseMessage(trans('common.changing_merge_color_subvalue'), self::MAP_EXIST, null,-1));
        }
        $currentExistSelect = FertilizerMapInfoFacade::selectModel()
            ->where('r', $currentSelect->r)
            ->where('g', $currentSelect->g)
            ->where('b', $currentSelect->b)
            ->where('main_fertilizer', $currentSelect->main_fertilizer)
            ->where('sub_fertilizer', $currentSelect->sub_fertilizer)->lists('id')->toArray();


        //check main sub
        $tmpSubMain = FertilizerMapInfoFacade::selectModel()
            ->where('fertilizer_id', $currentSelect->fertilizer_id)
            ->whereNotIn('id', $currentExistSelect)
            ->whereRaw('( ROUND(main_fertilizer) = '.$postData['main_barrel'].' and ROUND(sub_fertilizer) = '.$subBarrel.' )')
            ->lists('id')->toArray();

        $tmpColor = FertilizerMapInfoFacade::selectModel()
            ->where('fertilizer_id', $currentSelect->fertilizer_id)
            ->whereNotIn('id', $currentExistSelect)
            ->whereRaw('( r = '.$r.' and g = '.$g.'  and b = '.$b.' )')
            ->lists('id')->toArray();

        if (!empty($tmpColor) || !empty($tmpSubMain)) {
            throw new GisException(trans('common.same_fertilizer_submain'));
        }
        return null;
    }

    /**
     * Save color for fertilizer map
     * @param $postData
     * @return bool
     * @throws GisException
     * Step 2 : Change color
     */

    public function saveColorDetails($postData)
    {
        $currentSelect = FertilizerMapInfoFacade::selectModel()->find($postData['CurrentColors']);
        $fertilizer = FertilizerMapFacade::selectModel()->find($currentSelect->fertilizer_id);
        $subValue = 0;
        $subValueSelect = 0;
        $existMap = null;
        $responseExistMap = null;
        list($r, $g, $b) = explode(',', $postData['ColorCode']);
        if (empty($postData['IsOnebarrel'])) {
            $subValue =  $postData['sub_barrel'];
            $subValueSelect = $currentSelect->sub_fertilizer;
        }
        if (! empty($postData['update_colors'])) {
            $existMap = FertilizerMapInfoFacade::selectModel()
                ->where('fertilizer_id', $currentSelect->fertilizer_id)
                ->whereRaw('( ROUND(main_fertilizer) = '.$postData['main_barrel'].' and ROUND(sub_fertilizer) = '.$subValue.' )')
                ->whereRaw('("r" = ' . $r . ' and "g" = ' . $g . ' and "b" = ' . $b . ' )')->first();

        }

        $allMap = FertilizerMapInfoFacade::selectModel()
            ->where('fertilizer_id', $currentSelect->fertilizer_id)
            ->where('r', $currentSelect->r)
            ->where('g', $currentSelect->g)
            ->where('b',  $currentSelect->b)
            ->where('main_fertilizer',$currentSelect->main_fertilizer)
            ->where('sub_fertilizer',$currentSelect->sub_fertilizer)
            ->get()
            ->toArray();
        DB::beginTransaction();

        try {
            foreach ($allMap as $item) {

                $attributes = array(
                    'main_fertilizer' =>   $postData['main_barrel'],
                    'sub_fertilizer' =>  $subValue,
                    'r' =>  $r,
                    'g' =>  $g,
                    'b' =>  $b
                );
                $attributes = $this->modifyData($attributes);
                FertilizerMapInfoFacade::update($attributes, $item['id']);
            }
            DB::commit();
            MapTools::makeFertilizerFile($fertilizer->id,$fertilizer->user_id);
            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new GisException(trans('common.fertilizer_can_not_merging_admin'));
        }
    }

    /**
     * Save color for fertilizer map after modifying
     * @param $postData
     * @return bool
     * @throws GisException
     */
    public function saveMergingColor($postData){
        $isBin = $this->getIsBinFertilizerMap($postData['layerId']);
        if ($isBin) {
            throw new GisException(trans('common.parent_fertilizer_map_is_bin'));
        }

        $listCurrent = $postData['mapInfoIds'];
        $fertilizer = FertilizerMap::where('layer_id', $postData['layerId'])->first();
        $subBarrel = empty($postData['isOneBarrel']) ? $postData['sub_barrel'] : 0;
        list ($r, $g, $b) = explode(",", $postData['colorCode']);


        $existMapWithSubMain = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw('( ROUND(main_fertilizer) = '.$postData['main_barrel'].' and ROUND(sub_fertilizer) = '.$subBarrel.' )')
            ->whereRaw('( r != '.$r.' or g != '.$g.'  or b != '.$b.' )')
            ->get();

        $existMapWithColor = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw('( r = '.$r.' and g = '.$g.'  and b = '.$b.' )')
            ->whereRaw('( ROUND(main_fertilizer) != '.$postData['main_barrel'].' or ROUND(sub_fertilizer) != '.$subBarrel.' )')
            ->get();
        $existMapWithSubMainArray = $existMapWithSubMain->toArray();
        $existMapWithColorArray = $existMapWithColor->toArray();
        if (!empty($existMapWithSubMainArray) || !empty($existMapWithColorArray)) {
            throw new GisException(trans('common.same_fertilizer_submain'));
        }
        DB::beginTransaction();
        try {
            foreach ($postData['mapInfoIds'] as $item) {
                $tmp_attributes = array(
                    'main_fertilizer' => $postData['main_barrel'],
                    'sub_fertilizer' => $subBarrel,
                    'r' => $r,
                    'g' => $g,
                    'b' => $b
                );
                $tmp_attributes = $this->modifyData($tmp_attributes);
                FertilizerMapInfoFacade::update($tmp_attributes, $item);
            }
            DB::commit();
            MapTools::makeFertilizerFile($fertilizer->id,$fertilizer->user_id);
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new GisException(trans('common.fertilizer_can_not_merging_admin'));
        }
        return true;


    }

    /**
     * Verify whether the list of colors is valid or not
     * @param $postData
     * @return array|null
     */
    public function validColorListDetails($postData)
    {

        if (! empty($postData['update_colors'])) {
            return null;
        }

        list ($r, $g, $b) = explode(",", $postData['color_code']);

        $fertilizer = FertilizerMap::where('layer_id', $postData['fertilizerId'])->first();

        if(empty($fertilizer)){
            return buildResponseMessage(trans('common.fertility_maps_not_exists'), 1, null, -1);
        }
        $subBarrel = empty($postData['is_one_barrel']) ? $postData['sub_barrel'] : 0;
        $existMapFull = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw(DB::raw("ROUND(main_fertilizer) = {$postData['main_barrel']}"))
            ->whereRaw(DB::raw("ROUND(sub_fertilizer) = {$subBarrel}"))
            ->whereIn('id',explode(',',$postData['colorSelectIds']))
            ->where('sub_fertilizer', $subBarrel)
            ->where('r', $r)
            ->where('g', $g)
            ->where('b', $b)
            ->get()->toArray();
        if(!empty($existMapFull)){
            return buildResponseMessage(trans('common.same_fertilizer_submain'),  self::MAP_EXIST, null, -1);
        }
        //add more cr 29/09
        $arrayIds = explode(",", $postData['colorIds']);
        $existMapNotChangingColor = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw(DB::raw("ROUND(main_fertilizer) = {$postData['main_barrel']}"))
            ->whereRaw(DB::raw("ROUND(sub_fertilizer) = {$subBarrel}"))
            ->whereIn('id',explode(',',$postData['colorSelectIds']))
            ->get()->toArray();

        $existMapNotChangingColorCurrent = FertilizerMapInfoFacade::selectModel()
            ->where('fertilizer_id', $fertilizer->id)
            ->where('id',$arrayIds[0])
            ->where('r', $r)
            ->where('g', $g)
            ->where('b', $b)
            ->get()->toArray();

        if(!empty($existMapNotChangingColorCurrent) && !empty($existMapNotChangingColor)){
            return buildResponseMessage(trans('common.same_other_fertilizer_submain'),  self::MAP_EXIST, null, -1);
        }
        $existMap = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw(DB::raw("ROUND(main_fertilizer) = {$postData['main_barrel']}"))
            ->whereRaw(DB::raw("ROUND(sub_fertilizer) = {$subBarrel}"))
            ->whereNotIn('id',explode(',',$postData['colorSelectIds']))
            ->get()->toArray();


        $existMapRGB = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->where('r', $r)
            ->where('g', $g)
            ->where('b', $b)
            ->whereNotIn('id',explode(',',$postData['colorSelectIds']))
            ->get()->toArray();

        if (! empty($existMap) || ! empty($existMapRGB)) {
            return buildResponseMessage(trans('common.same_other_fertilizer_submain'), 1, null, -1);
        }


        $existMap = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw(DB::raw("ROUND(main_fertilizer) = {$postData['main_barrel']}"))
            ->whereRaw(DB::raw("ROUND(sub_fertilizer) = {$subBarrel}"))
            ->whereIn('id',explode(',',$postData['colorSelectIds']))
            ->get()->toArray();

        $existMapRGB = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->where('r', $r)
            ->where('g', $g)
            ->where('b', $b)
            ->whereIn('id',explode(',',$postData['colorSelectIds']))
            ->get()->toArray();
        if(!empty($existMap) || !empty($existMapRGB)) {
            return buildResponseMessage(trans('common.same_fertilizer_submain'),  self::MAP_EXIST, null, -1);
        }
        return null;
    }

    /**
     * Save the color for fertilizer map after modifying
     * @param $postData
     * @return \Illuminate\Http\JsonResponse
     * @throws GisException
     */
    public function saveEditingColor($postData)
    {
        $arrayIds = explode(",", $postData['colorIds']);
        $colorSelectIds = explode(",", $postData['colorSelectIds']);
        list ($r, $g, $b) = explode(",", $postData['color_code']);

        $fertilizer = FertilizerMap::where('layer_id', $postData['fertilizerId'])->first();
        $subBarrel = empty($postData['is_one_barrel']) ? $postData['sub_barrel'] : 0;
        //

        $existMapFull = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw(DB::raw("ROUND(main_fertilizer) = {$postData['main_barrel']}"))
            ->whereRaw(DB::raw("ROUND(sub_fertilizer) = {$subBarrel}"))
            ->whereIn('id',explode(',',$postData['colorSelectIds']))
            ->where('r', $r)
            ->where('g', $g)
            ->where('b', $b)
            ->get();
        $existMapFullArray = $existMapFull->toArray();

        if(!empty($existMapFullArray)){
            return $this->mergeExistMap(null, $arrayIds, $postData['main_barrel'],
                $r, $g, $b,
                $subBarrel, $fertilizer, $colorSelectIds);
        }

        $existMapNotChangingColor = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw(DB::raw("ROUND(main_fertilizer) = {$postData['main_barrel']}"))
            ->whereRaw(DB::raw("ROUND(sub_fertilizer) = {$subBarrel}"))
            ->whereIn('id',explode(',',$postData['colorSelectIds']))
            ->get()->toArray();


        $existMapNotChangingColorCurrent = FertilizerMapInfoFacade::selectModel()
            ->where('fertilizer_id', $fertilizer->id)
            ->where('id',$arrayIds[0])
            ->where('r', $r)
            ->where('g', $g)
            ->where('b', $b)
            ->get()->toArray();

        if(!empty($existMapNotChangingColorCurrent) && !empty($existMapNotChangingColor)){

            return $this->mergeExistMap(null, $arrayIds, $postData['main_barrel'],
                $existMapNotChangingColor[0]['r'], $existMapNotChangingColor[0]['g'],
                $existMapNotChangingColor[0]['b'], $subBarrel, $fertilizer, $colorSelectIds);
        }
        $existMap = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereRaw(DB::raw("ROUND(main_fertilizer) = {$postData['main_barrel']}"))
            ->whereRaw(DB::raw("ROUND(sub_fertilizer) = {$subBarrel}"))
            ->whereIn('id', $colorSelectIds)
            ->get();
        //add more cr
        $tmpArray = $existMap->toArray();
        if (! empty($tmpArray)) {


            $existCurrentMap = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
                ->where('r', '!=', $r)
                ->where('g', '!=', $g)
                ->where('b', '!=', $b)
                ->whereIn('id', $arrayIds)
                ->first();

            if ($existCurrentMap) {

                $existMap[0]->r = $r;
                $existMap[0]->g = $g;
                $existMap[0]->b = $b;
            }
            return $this->mergeExistMap($existMap, $arrayIds, $postData['main_barrel'],
                $existMap[0]->r, $existMap[0]->g, $existMap[0]->b, $subBarrel, $fertilizer, $colorSelectIds);
        }

        $colorListUpdate = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
            ->whereNotIn('id', $colorSelectIds)
            ->where('r', $r)
            ->where('g', $g)
            ->where('b', $b)
            ->whereRaw(DB::raw("ROUND(main_fertilizer) != {$postData['main_barrel']}"))
            ->whereRaw(DB::raw("ROUND(sub_fertilizer) != {$subBarrel}"))
            ->get();
        $tmpArray = $colorListUpdate->toArray();
        if (! empty($tmpArray)) {
            return $this->mergeExistMap($colorListUpdate, $arrayIds,$postData['main_barrel'],
                $colorListUpdate[0]->r, $colorListUpdate[0]->g, $colorListUpdate[0]->b,$subBarrel, $fertilizer, $colorSelectIds);
        }

        return $this->mergeExistMap(null, $arrayIds, $postData['main_barrel'], $r, $g, $b,
            $subBarrel, $fertilizer, $colorSelectIds);
    }

    /**
     * Modify color and volume of fertilizer map
     * @param $existMap
     * @param $arrayIds
     * @param $main_barrel
     * @param $r
     * @param $g
     * @param $b
     * @param $subBarrel
     * @param $fertilizer
     * @param $colorSelectIds
     * @return \Illuminate\Http\JsonResponse
     * @throws GisException
     */
    protected function mergeExistMap($existMap, $arrayIds, $main_barrel, $r, $g, $b, $subBarrel, $fertilizer, $colorSelectIds)
    {
        DB::beginTransaction();
        $existListMap = [];
        foreach ($arrayIds as $key) {
            $current = FertilizerMapInfoFacade::find($key);
            $tmpMain = round($current->main_fertilizer) ;
            $tmpSub =  round($current->sub_fertilizer);
            $existListMap[] = FertilizerMapInfoFacade::selectModel()->where('fertilizer_id', $fertilizer->id)
                ->whereIn('id', $colorSelectIds)
                ->where('r', $current->r)
                ->where('g', $current->g)
                ->where('b', $current->b)
                ->whereRaw(DB::raw("ROUND(main_fertilizer) = $tmpMain"))
                ->whereRaw(DB::raw("ROUND(sub_fertilizer) = $tmpSub"))
                ->get();
        }
        try {

            foreach ($existListMap as $listMap) {

                foreach ($listMap as $key) {

                    $tmp_attributes = array(
                        'main_fertilizer' => $main_barrel,
                        'sub_fertilizer' => $subBarrel,
                        'r' => $r,
                        'g' => $g,
                        'b' => $b
                    );
                    $tmp_attributes = $this->modifyData($tmp_attributes);
                    FertilizerMapInfoFacade::update($tmp_attributes, $key->id);
                }
            }
            if ($existMap) {

                foreach ($existMap as $key) {
                    $tmp_attributes = array(
                        'main_fertilizer' => $main_barrel,
                        'sub_fertilizer' => $subBarrel,
                        'r' => $r,
                        'g' => $g,
                        'b' => $b
                    );
                    $tmp_attributes = $this->modifyData($tmp_attributes);
                    FertilizerMapInfoFacade::update($tmp_attributes, $key->id);
                }
            }

            DB::commit();
            MapTools::makeFertilizerFile($fertilizer->id,$fertilizer->user_id);
            return response()->json(buildResponseMessage(trans('common.save_success'), 1, null, null));
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new GisException(trans('common.fertilizer_can_not_merging_admin'));
        }
    }

    /**
     * Modify color of fertilizer map
     * @param $postData
     * @throws GisException
     */
    public function  mergeColorCurrentFertilizerMap($postData){
        $layers = FertilizerMap::where('layer_id', $postData['layerID'])->first();
        $current = FertilizerMapInfo::find($postData['currentID']);
        $fertilizer = FertilizerMap::where('layer_id',$postData['layerID'])->first();

        if (empty($layers) || empty($current) || empty($fertilizer) || empty($postData['mapInfoIds'])) {
            throw new GisException(trans('common.fertilizer_map_not_found'), SystemCode::NOT_FOUND);
        }
        $listFertilzier = [];
        try {
            foreach ($postData['mapInfoIds'] as $id) {

                $tmp_attributes = array(
                    'main_fertilizer' => $current->main_fertilizer,
                    'sub_fertilizer' => $current->sub_fertilizer,
                    'r' => $current->r,
                    'g' => $current->g,
                    'b' => $current->b
                );
                $tmp_attributes = $this->modifyData($tmp_attributes);
                FertilizerMapInfoFacade::update($tmp_attributes, $id);

            }
            DB::commit();
            MapTools::makeFertilizerFile($fertilizer->id,$fertilizer->user_id);
            return true;
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new GisException(trans('common.fertilizer_can_not_merging_admin'));
        }

    }

    /**
     * Build response data to filteer
     *
     * @param LengthAwarePaginator $dataPagging
     * @param int $currentPage
     * @return aray() $response
     */
    public function buildResponser(LengthAwarePaginator $dataPagging, $currentPage)
    {
        $results = array();
        if (! $dataPagging->isEmpty()) {
            if (! $dataPagging->isEmpty()) {
                foreach ($dataPagging as $obj) {
                    $obj->parrent = $obj->crop;
                    $obj->fertilization_standard_name = htmlspecialchars($obj->fertilization_standard_name);
                    $obj->range_of_application = htmlspecialchars($obj->range_of_application);
                    $obj->notes = htmlspecialchars($obj->notes);
                    $obj->remarks = htmlspecialchars($obj->remarks);
                    array_push($results, $obj);
                }
            }
        }
        $response = array(
            'page' => ($dataPagging->isEmpty()) ? $dataPagging->currentPage() : $currentPage,
            'total' => ($dataPagging->isEmpty()) ? 1 : $dataPagging->lastPage(),
            'records' => ($dataPagging->isEmpty()) ? 0 : $dataPagging->total(),
            'rows' => $results
        );

        return $response;
    }

    /**
     * Save fertilizer price
     * @param $postData
     * @return mixed
     */
    public function saveFertilizerPrice($postData)
    {
        $attributes = array(
            'price' => $postData['price'],
            'start_date' => $postData['start_date'],
            'end_date' => $postData['end_date']
        );
        $attributes = $this->modifyData($attributes, true);
        FertilizationPriceFacade::create($attributes);

        return response()->json(buildResponseMessage(trans('common.save_success'), 1, null, null));
    }
    /**
     *Get the fertilizer map
     * @param
     *            $layer_id
     * @return mixed
     */
    public function getBuyFertilizerData($layer_id)
    {
        $result = array();
        $fertilizer_map = FertilizerMapFacade::findByLayerId($layer_id);
        if($fertilizer_map==NULl){
            throw new GisException(trans("common.fertilizer_map_not_found"),SystemCode::NOT_FOUND);
        }
        $result['unitPrice'] = $fertilizer_map->getUnitPrice();
        $result['id'] = $this->generateCode();
        $fertilizer_map_id = $fertilizer_map->id;
        $cropID = FertilizerMapPropertyFacade::getCropID($fertilizer_map_id);
        $meshSize = FertilizerMapPropertyFacade::findByField('fertilizer_map_id',$fertilizer_map_id)->first();
        $result['meshSize'] = $meshSize->mesh_size;
        $cropName = CropFacade::getCropName($cropID['crops_id']);
        $result['cropName'] = $cropName['crops_name'];
        return $result;
    }

    /**
     * Generate the unique id
     * @return string
     */
    public function generateCode()
    {
        $az = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $azr = rand(0, 61);
        $azs = substr($az, $azr, 10);
        $stamp = hash('sha256', time());
        $mt = hash('sha256', mt_rand(5, 20));
        $alpha = hash('sha256', $azs);
        $hash = str_shuffle($stamp . $mt . $alpha);
        $code = ucfirst(substr($hash, $azr, 10));
        return $code;
    }

    /**
     *Get the properties for fertilizer map
     * @param
     *            $layerid
     * @return \stdClass
     */
    public function fertilizerPropetiesData($layerid)
    {
        $data = new \stdClass();
        $data->fertilizerMap = FertilizerMapFacade::findByLayerId($layerid);
        if($data->fertilizerMap==NULl){
            throw new GisException(trans("common.fertilizer_map_not_found"),SystemCode::NOT_FOUND);
        }
        $data->fertilizerMapProperty = FertilizerMapPropertyFacade::findByField('fertilizer_map_id', $data->fertilizerMap->id)->first();
        $data->crops = CropFacade::find($data->fertilizerMapProperty->crops_id);
        $data->fertilizerStandardDefinition = FertilizerFacade::findByField('id', $data->fertilizerMapProperty->fertilizer_standard_definition_id)->first();
        return $data;
    }

    /**
     * Get User fertilizer definition detail Id with fertilzier standard
     *
     * @param int $fertilizerDefineId
     * @param int $cropsId
     *
     * @return array() $reponse
     */
    public function getUserStandardId($fertilizerDefineId, $cropsId)
    {
        $fertilizerStandard = $this->getById($fertilizerDefineId);
        $standardCrop = StandardCropFacade::getByCropId($fertilizerDefineId, $cropsId);

        return array(
            'isSystem' => $fertilizerStandard->created_by ? false : true,
            'userStandardId' => empty($standardCrop) ? null : $standardCrop->id
        );
    }

    /**
     * get fertilizer area selection
     * @param $layerId
     * @return int
     */
    public function getAreaSelection($layerId)
    {
        $fertilizerId = FertilizerMapFacade::findByField('layer_id', $layerId)->first();
        $fertilitySelection = FertilityMapSelectionFacade::findByField('fertilizer_map_id', $fertilizerId->id)->first();
        $mapInfoId = FertilityMapSelectionInfoFacade::findByField('fertility_map_selection_id', $fertilitySelection->id)->all();
        return count($mapInfoId);
    }

    /**
     * get crop id of fertilizer map
     * @param $layerId
     * @return mixed
     */
    public function getCropIdFertilizerMap($layerId)
    {
        $fertilizer = FertilizerMapFacade::findByField('layer_id', $layerId)->first();
        $cropId = FertilizerMapPropertyFacade::findByField('fertilizer_map_id', $fertilizer->id)->first();
        return $cropId->crops_id;
    }

    /**
     * Get Fertilizer map By Id
     *
     * @param unknown $fertilizerMapId
     *
     * @return Models\Entities\FertilizerMap $fertilizerMap
     */
    public function getFertilizerMapById($fertilizerMapId)
    {
        $fertilizerMap = FertilizerMapFacade::findByField('id', $fertilizerMapId)->first();
        if (empty($fertilizerMap)){
            throw new GisException(trans('common.fertilizer_map_not_found'), SystemCode::NOT_FOUND);
        }
        return $fertilizerMap;
    }

    /**
     * Show the download history grid for user
     * @param string $resource
     * @param null $pagingRequest
     * @param array $postData
     * @param bool|true $pagingStatus
     * @return array
     * @throws GisException
     */
    public function gridGetAll($resource = 'mappayment', $pagingRequest = null, $postData = array(), $pagingStatus = true)
    {

        $fields = array(
            'fertilizer_map_payments.*',
            'u.username',
            'ug.group_name'
        );

        $limit = empty($pagingRequest['rows']) ? self::PAYMENT_LIMIT_PER_PAGE : $pagingRequest['rows'];

        $payments = FertilizerMapPaymentFacade::selectModel()->orderBy($pagingRequest['sidx'],$pagingRequest['sord']);

        if (! empty($postData['download_date_start']) && ! empty($postData['download_date_end'])) {
            $payments->where("download_date", '>=', Carbon::parse($postData['download_date_start'])->format('Y-m-d 00:00:00.0'));
            $payments->where("download_date", '<=', Carbon::parse($postData['download_date_end'])->format('Y-m-d 12:59:59.0'));
        } elseif (! empty($postData['download_date_start']) && empty($postData['download_date_end'])) {
            $payments->where("download_date", '>=', Carbon::parse($postData['download_date_start'])->format('Y-m-d 00:00:00.0'));
        } elseif (! empty($postData['download_date_end']) && empty($postData['download_date_start'])) {
            $payments->where("download_date", '<=', Carbon::parse($postData['download_date_end'])->format('Y-m-d 12:59:59.0'));
        }

        if (! empty($postData['download_id'])) {
            $payments->where('download_id', $postData['download_id']);
        }
        if (isset($postData['paymentState']) && $postData['paymentState'] != null) {
            $is_paid = explode(',', $postData['paymentState']);
            $payments->whereIn('is_paid', $is_paid);
        }
        if (! empty($postData['user_code'])) {
            $payments->where('fertilizer_map_payments.user_code', $postData['user_code']);
        }

        $payments->join('users AS u', 'fertilizer_map_payments.user_code', '=', 'u.user_code');
        if (! empty($postData['user_name'])) {
            $payments->where('u.username', 'like', '%' . $postData['user_name'] . '%');
        }
        $payments->join('usergroups AS ug', 'u.user_group_id', '=', 'ug.id');
        if (! empty($postData['user_group'])) {
            $payments->where('ug.group_name', $postData['user_group']);
        }

        $payments->select($fields);
        $records = $pagingStatus ? $payments->paginate($limit) : $payments->get();

        foreach ($records as &$item) {
            $item->user_name = $item->username;
            $item->download_date = ! empty($item->download_date) ? date('Y-m-d H:i:s', strtotime($item->download_date)) : '';
            $item->user_group = $item->group_name;
            $item->map_name = $item->fertilizer_maps->folderLayer->name;
            $item->crop_map = CropFacade::getCropName($item->fertilizer_maps->fertilizerMapProperty->crops_id)->crops_name;
            $item->mesh_size = $item->fertilizer_maps->fertilizerMapProperty->mesh_size;
            $item->payment = $item->is_paid;
            $item->price = $this->calculatePrice($item->unit_price, $item->area); // (area * unit )/10
            $item->id = $item->id;
        }
        if (! $pagingStatus) {
            if(!count($records)){
                throw new GisException(trans('common.emptyrecords'));
            }
            return $records;
        }


        return array(
            'page' => $records->currentPage(),
            'total' => $records->lastPage() ? $records->lastPage() : 1,
            'records' => $records->total(),
            'rows' => $records->items()
        );
    }

    /**
     * Calcualte price for fertilizer map
     * @param $unit_price
     * @param $area
     * @return string
     */
    public function calculatePrice($unit_price, $area)
    {
        return number_format(round($unit_price * $area) / 10,0,null,',');
    }

    /**
     * Update fertilizer map price
     * @param $request
     * @return mixed
     * @throws GisException
     */
    public function activePayment($request)
    {
        if (empty($request['paymentId'])) {
            throw new GisException(trans('common.form_downloadmanagement_not_found'), SystemCode::NOT_FOUND);
        }
        if (mb_strlen(trim(strip_tags($request['remark']))) > 500) {
            throw new GisException(trans('common.download_management_overstring_condition'), SystemCode::NOT_FOUND);
        }

        DB::beginTransaction();
        try {
            $listId = explode(',', $request['paymentId']);
            foreach ($listId as $id) {
                if (empty($id)) {
                    continue;
                }
                $download = FertilizerMapPayment::find($id);
                $fertilizer_map_id = $download->fertilizer_maps->id;
                $fertilityMapSelection = FertilityMapSelection::where('fertilizer_map_id', $fertilizer_map_id)->first();
                if (isset($request['pid'])) {
                    $download->is_paid = true;
                    if (!empty($fertilityMapSelection)) {
                        $fertilityMapSelection->is_ready = true;
                    }
                    $download->payment_date = Carbon::now()->format('Y-m-d H:i:s');
                } else {
                    if (!empty($fertilityMapSelection)) {
                        $fertilityMapSelection->is_ready = false;
                    }
                    $download->is_paid = false;
                    $download->payment_date = null;

                }
                if (!empty($fertilityMapSelection)) {
                    $fertilityMapSelection = $this->modifyObject($fertilityMapSelection);
                    $fertilityMapSelection->save();
                }


                $download->remark = trim(strip_tags($request['remark'])) ;
                $download = $this->modifyObject($download, false);
                $download->save();
                DB::commit();
            }

            return response()->json(buildResponseMessage(trans('common.download_save_success'), 1, null, null));
        } catch (\Exception $ex) {
            DB::rollBack();
            throw new GisException(trans('common.form_downloadmanagement_not_udpate_found'));
        }
    }

    /**
     * User request an order for fertilizer map
     *
     * @param
     *            $postData
     * @return mixed
     */
    public function createFertilityMapPayment($postData)
    {
        if(!session('user')->usergroup->auth_authorization) {
            DB::beginTransaction();
            $attributes['fertilizer_id'] = $postData['fertilizer_id'];
            $attributes['download_date'] = $postData['download_date'];
            $attributes['user_code'] = $postData['user_code'];
            $attributes['unit_price'] = $postData['unit_price'];
            $attributes['area'] = $postData['area'];
            $attributes['download_id'] = $postData['download_id'];
            $attributes['is_paid'] = false;
            $attributes['crops_id'] = $postData['crops_id'];
            $attributes = $this->modifyData($attributes, true);
            //check if user downloads fertilizer map again and user did not change the crops of fertilizer
            //then the area and price =0
            FertilizerMapPaymentFacade::create($attributes);
            // update fertility selection map
            $fertilityMapSelection = FertilityMapSelection::where('fertilizer_map_id', $postData['fertilizer_id'])->first();
            $fertilityMapSelection->is_ready = true;
            $fertilityMapSelection->save();
            DB::commit();
        }

        //Add application log
        ApplicationLogFacade::logAction(LoggingAction::ACTION_CREATE_HISTORY_DOWNLOAD, $postData);
    }

    /**
     * Check whether open popup or not for end-user
     * @param $layerId
     * @return int
     */
    public function canShowPopup($layerId)
    {
        $arrRet = array(
            'canShowPopup'=>false,
            'unpaidMesh'=>0
        );

        // we only display for end user with following conditions:
        //-- if this is first download time
        //-- if already downloaded but user changed crops
        $unpaidMesh = $this->getTotalAmountOfUnpaidMesh($layerId);
        if($unpaidMesh ==0 ){
            //already download for the same crops
            //so do not display popup
            $arrRet['canShowPopup'] =false;
            $arrRet['unpaidMesh'] =$unpaidMesh;
        }
        else if ($unpaidMesh >0) {
            //user doesn't need to pay for this download, just download immediately
            $arrRet['canShowPopup'] =true;
            $arrRet['unpaidMesh'] =$unpaidMesh;
        }
        return $arrRet;
    }

    /**
     * Get total amount of unpaid mesh
     * @param $layerId
     * @return int
     */
    public function getTotalAmountOfUnpaidMesh($layerId)
    {
        // get other fertilizer map id that have the same crops and year
        $fertilizerIds = FertilizerMapPaymentFacade::getListOfFertilizerIds($layerId);
        $unpaidMapinfos = FertilizerMapPaymentFacade::getListMapInfo($layerId, $fertilizerIds);
        if ($unpaidMapinfos) {
            // if unpaid map info is not null then display the popup for user to download
            return count($unpaidMapinfos);
        } else {
            // if unpaid map info is null then the current fertilizer is paid and can download now
            // and insert a new record to payment table with area=0
            return 0;
        }
        // get unpaid map info
    }

    /**
     * Update the download date only when download the fertilizer map
     *
     * @param
     *            $fertilizerMapId
     */
    public function updateDownloadDate($fertilizerMapId)
    {
        FertilizerMapPaymentFacade::updateDownloadDate($fertilizerMapId);
    }
    public function getFertilizerMapInfoById($id)
    {
        $current = FertilizerMapInfo::find($id);
        if (empty($current)) {
            throw new GisException(trans('common.fertilizer_map_not_found'), SystemCode::NOT_FOUND);
        }
        return $current;
    }
}