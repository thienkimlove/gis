<?php
namespace Gis\Models\Repositories;

use Gis\Models\Entities\FertilizerDetail;
use Gis\Models\Entities\SystemFertilizerDefinitionDetailNito;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use Gis\Models\Entities\FertilizerMap;
use Gis\Models\Entities\FertilizerMapProperty;
use Gis\Models\Entities\FertilizationDivision;
use Gis\Models\Entities\StandardCropKali;
use Gis\Models\Entities\StandardCropPhotpho;
use Gis\Models\Entities\FertilizerStage;
use Gis\Models\Entities\OrganicMatterField;
use Gis\Models\Entities\SystemFertilizerDefinitionDetailKali;
use Gis\Models\Entities\SystemFertilizerDefinitionDetailPhotpho;
use Gis\Models\Entities\FertilizerMapInfo;
use Illuminate\Support\Facades\DB;
use Gis\Exceptions\GisException;

/**
 * Fertilizer repository provider functional access to database.It like same data provider layer.
 * Class FertilizerRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class FertilizerRepositoryEloquent extends BaseRepository implements FertilizerRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\Fertilizer';
    }

    public function selectModel()
    {
        return $this->model;
    }

    public function getFertilizers($limit, $orderBy, $orderType, $userCode, $isAdmin)
    {
        if ($isAdmin) {
            $result = $this->model->orderBy($orderBy, $orderType);
            return $result->paginate($limit);
        }

        $result = $this->model->where('ins_user', '=', $userCode)
            ->orWhere('created_by', true)
            ->orderBy($orderBy, $orderType);
        return $result->paginate($limit);
    }

    public function getStandardCropDetails($standardCropId, $orderBy = 'id', $orderType = 'asc')
    {}

    public function deleteMany(array $ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function getLimitFertilizers($keyword = null)
    {
        $result = $this->model->where('fertilization_standard_name', 'like', '%' . $keyword . '%')->orderBy('fertilization_standard_name');
        return $result->paginate(15);
    }

    /**
     * Insert fertilizer map data to db
     *
     * @param array() $attributes
     * @return Gis\Models\Entities\FertilizerMap $fertilizerMap
     */
    public function createFertilizerMap($attributes)
    {
        $fertilizerMap = FertilizerMap::create($attributes);
        return $fertilizerMap;
    }

    /**
     * Insert fertilizer property data to db
     *
     * @param array() $attributes
     * @return Gis\Models\Entities\FertilizerMapProperty $fertilizerproperty
     */
    public function createFertilizerProperty($attributes)
    {
        $fertilizerProperty = FertilizerMapProperty::create($attributes);
        return $fertilizerProperty;
    }

    /**
     * Filter fertilizer property fields
     *
     * @param array() $postData
     *
     * @return array() $propertyData
     */
    public function filterPropertyData($postData)
    {
        $fertilizerProperty = new FertilizerMapProperty();
        return $fertilizerProperty->filterDataFromArray($postData);
    }

    /**
     * Get Kali Of user define by ratio & user define id
     *
     * @param int $userDefineId
     * @param float $ratio
     *
     * @return Gis\Models\Entities\StandardCropKali $StandardCropKali
     *
     */
    public function getDefineKByInfomation($userDefineId, $ratio)
    {
        return StandardCropKali::Select('fertilization_standard_amount')->where('ratio', $ratio)
            ->where('user_fertilizer_definition_detail_id', $userDefineId)
            ->first();
    }

    /**
     * Get Photpho Of user define by ratio & user define id
     *
     * @param int $userDefineId
     * @param float $ratio
     *
     * @return Gis\Models\Entities\StandardCropPhotpho $StandardCropPhotpho
     *
     */
    public function getDefinePByInfomation($userDefineId, $ratio)
    {
        return StandardCropPhotpho::Select('fertilization_standard_amount')->where('ratio', $ratio)
            ->where('user_fertilizer_definition_detail_id', $userDefineId)
            ->first();
    }

    /**
     * Get Kali Of system by ratio & fertilizer standard id & crop id
     *
     * @param int $standardId
     * @param crop $cropId
     * @param float $ratio
     *
     * @return Gis\Models\Entities\SystemFertilizerDefinitionDetailKalis $StandardKali
     *
     */
    public function getSystemKByInfomation($standardId, $cropId, $ratio)
    {
        return SystemFertilizerDefinitionDetailKali::Select('fertilization_standard_amount')->where('ratio', $ratio)
            ->where('fertilizer_standard_definition_id', $standardId)
            ->where('crops_id', $cropId)
            ->first();
    }

    /**
     * Get Photpho Of system by ratio & fertilizer standard id & crop id
     *
     * @param int $standardId
     * @param crop $cropId
     * @param float $ratio
     *
     * @return Gis\Models\Entities\SystemFertilizerDefinitionDetailphotpho $StandardPhotpho
     *
     */
    public function getSystemPByInfomation($standardId, $cropId, $ratio)
    {
        return SystemFertilizerDefinitionDetailPhotpho::Select('fertilization_standard_amount')->where('ratio', $ratio)
            ->where('fertilizer_standard_definition_id', $standardId)
            ->where('crops_id', $cropId)
            ->first();
    }

    /**
     * Get P-K in fertilization division
     * by fertilizty map id
     *
     * @param int $fertilityMapId
     * @param int $cropsCode
     *
     * @return Gis\Models\Entities\FertilzationDevision $fettilizationDevision
     *
     */
    public function getDivisionPkByFertilityMapId($fertilityMapId, $cropsCode)
    {
        return FertilizationDivision::Select('n', 'p', 'k')
            ->where('fertilization_classification_code', function ($query) use($fertilityMapId)
            {
                $query->select('fertilization_classification_code')
                    ->from('fertility_map_infos')
                    ->where('fertility_id', $fertilityMapId)
                    ->first();
            })
            ->where('crops_code', $cropsCode)
            ->distinct()
            ->get();
    }

    /**
     * Get Fertilization division by list of fertility map info ids and crop id
     * @param $fertilityMapIds
     * @param $cropsCode
     * @return mixed
     */
    public function getDivisionByFertilityMapInfoIdsAndCropCode($fertilityMapIds, $cropsCode)
    {
        return FertilizationDivision::Select('n', 'p', 'k')
            ->whereIn('fertilization_classification_code', function ($query) use($fertilityMapIds)
            {
                $query->select('fertilization_classification_code')
                    ->from('fertility_map_infos')
                    ->whereIn('id', $fertilityMapIds);
            })
            ->where('crops_code', $cropsCode)
            ->get();
    }

    /**
     * Create new Stage with attributes
     *
     * @param array() $attributes
     *
     * @return boolean
     */
    public function createStages($attributes)
    {
        return FertilizerStage::insert($attributes);
    }

    /**
     * Create new Fertilizer Details with attributes
     *
     * @param array() $attributes
     *
     * @return boolean
     */
    public function createDetails($attributes)
    {
        return FertilizerDetail::insert($attributes);
    }

    /**
     * Create new Organic matter fields
     *
     * @param array() $attributes
     *
     * @return boolean
     */
    public function createOrganicMatterFields($attributes)
    {
        return OrganicMatterField::insert($attributes);
    }

    /**
     * Create new fertilizer map info
     *
     * @param array() $attributes
     *
     * @return boolean
     */
    public function createFertilizerMapInfos($attributes)
    {
        return FertilizerMapInfo::insert($attributes);
    }

    /**
     * Load nito amount standard of system by amount number
     *
     * @param String $amount
     * @param Array() $nitrogen
     * @param Int $fertilizerStandardId
     * @param Int $cropsId
     *
     * @return Illuminate\Database\Eloquent\Collection $collection
     */
    public function loadSystemStandardNitos($amount, $nitrogen, $fertilizerStandardId, $cropsId)
    {
        return SystemFertilizerDefinitionDetailNito::Select(array(
            'n',
            'n_amount',
            $amount
        ))->whereIn('n', $nitrogen)
            ->where('fertilizer_standard_definition_id', $fertilizerStandardId)
            ->where('crops_id', $cropsId)
            ->get();
    }

    /**
     * Create new fertilizer map info
     */
    public function createMapInfos($attributes)
    {
        return SystemFertilizerDefinitionDetailnito::insert($attributes);
    }

    /**
     * check Initial,Basic Exists in database
     *
     * @param
     *            $field
     * @return bool
     */
    public function checkInitialBasicExist($field)
    {
        $result = $this->model->where($field, '=', true)->first();
        return $result;
    }

    /**
     * delete Initial,Basic Exists in database
     *
     * @param
     *            $field
     */
    public function deleteInitialBasicExist($field)
    {
        $this->model->where($field, '=', true)->update([
            $field => false
        ]);
    }

    /**
     * Get Collection Fertilizer Details By Fertilzier ID
     *
     * @param int $fertilizerId
     *
     * @return Illuminate\Database\Eloquent\Collection $collection
     */
    public function getListDetailsByFertilizerId($fertilizerId)
    {
        return FertilizerDetail::Select(array(
            'id'
        ))->where('fertilizer_id', $fertilizerId)->get();
    }

    /**
     * Get Collection Fertilizer Details By IDs
     *
     * @param int $ids
     *
     * @return Illuminate\Database\Eloquent\Collection $collection
     */
    function getByIds($ids)
    {
        return $this->model->select(array(
            'fertilization_standard_name',
            'not_available',
            'initial_display',
            'created_by',
            'id'
        ))
            ->whereIn('id', $ids)
            ->orderBy('fertilization_standard_name', 'asc')
            ->get();
    }

    /**
     * Get fertilizer for User by crop Id
     * @param $cropId
     */
    public function getFertilizersByCropAndUserCode($cropId){
        if (!is_numeric($cropId)) {
            throw new GisException(trans('common.common_crop_id_invalid'), SystemCode::NOT_FOUND);
        }
        $user = session('user');
        $user_code = $user->user_code;
        $isAdmin = $user->usergroup->auth_authorization;
        $queryTemplate =null;
        $rawQuery = null;
        if(!$isAdmin){
            $queryTemplate = "
                        select *from fertilizer_standard_definitions where id in
                        (
                        select distinct fertilizer_standard_definition_id
                        From user_fertilizer_definition_details
                        where crops_id=%s
                        and fertilizer_standard_definition_id in
                        (select distinct id from fertilizer_standard_definitions
                        where ins_user =%s and not_available = false)
                        and not_available = false
                        union
                        select distinct fertilizer_standard_definition_id
                        from system_fertilizer_definition_detail_nitos where crops_id=%s
                        union
                        select distinct fertilizer_standard_definition_id
                        from fertilizer_standard_user_relations where user_code=%s
                        )
                        and not_available = false
                        order by fertilization_standard_name ASC
                        ;";
            $rawQuery = sprintf($queryTemplate,$cropId,$user_code,$cropId,$user_code);
        }
        else{
            $queryTemplate = "
            select *from fertilizer_standard_definitions where id in
            (
            select distinct fertilizer_standard_definition_id
            From user_fertilizer_definition_details
            where crops_id=%s
            and not_available = false
            union
            select distinct fertilizer_standard_definition_id
            from system_fertilizer_definition_detail_nitos where crops_id=%s
            )
            and not_available = false
            order by fertilization_standard_name ASC
            ;";
            $rawQuery = sprintf($queryTemplate,$cropId,$cropId);
        }


        $result = DB::select($rawQuery);
        return $result;
    }
}