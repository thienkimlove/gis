<?php

namespace Gis\Models\Repositories;
use Carbon\Carbon;
use DateTime;
use \Illuminate\Support\Facades\DB;
use Gis\Exceptions\GisException;

/**
 * Fertilizer repository provider functional access to database.It like same data provider layer.
 * Class FertilizerRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class FertilizerMapPaymentRepositoryEloquent extends GisRepository implements FertilizerMapPaymentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\FertilizerMapPayment';
    }

    /**
     * Check if the record existed or not
     * @param $fertilizerId
     * @return mixed
     */
    public function getListOfPaymentForWithTheSameCropsAndFertilizer($layerId){
        if (!is_numeric($layerId)) {
            throw new GisException(trans('common.common_layer_id_invalid'), SystemCode::NOT_FOUND);
        }
        $queryTemplate =" select*from fertilizer_map_payments where fertilizer_id =
(select id from fertilizer_maps where layer_id=%s)
and crops_id = (select crops_id from fertilizer_map_properties where fertilizer_map_id = (select id from fertilizer_maps where layer_id=%s));
 ";
        $rawQuery = sprintf($queryTemplate,$layerId,$layerId);
        return  DB::select($rawQuery);
    }

    /**
     * Update download date of fertilizer download history
     * @param $fertilizerId
     * @return mixed
     */
    public function updateDownloadDate($fertilizerId){
        return $this->model->where('fertilizer_id', $fertilizerId)
            ->update(['download_date' => Carbon::now(),'is_paid'=>true]);
    }

    /**
     * get list of paid fertilizer maps
     * @param $userCode
     * @return mixed
     */
    public function getListFertilizerIsPaid($userCode){
        return $this->model->where('user_code', $userCode)
            ->where('is_paid', true)->get('fertilizer_id')->all();
    }
    /**
     * Get all the fertilizer ids that have the same crops and year
     * and that're paid already
     */
    public function getListOfFertilizerIds($layerId){
        if (!is_numeric($layerId)) {
            throw new GisException(trans('common.common_layer_id_invalid'), SystemCode::NOT_FOUND);
        }
        //year start: 1/7/2015 0:00:00 year end: 30/06/2015 23:59:59
        $lastYearEnd = DateTime::createFromFormat('Y-m-d H:i:s', date("Y")."-06-30 23:59:59");
        //calculate to find out the start date
        $startDate = null;
        $currentDate = new DateTime('now');
        if($currentDate <=$lastYearEnd){
            $startDate = (date("Y")-1)."-07-01";
        }
        else if($currentDate > $lastYearEnd){
            $startDate = date("Y")."-07-01";
        }
        $queryTemplate = "select distinct fertilizer_map_payments.fertilizer_id,fertilizer_maps.fertility_map_id
         from fertilizer_map_payments,fertilizer_maps
        where fertilizer_map_payments.fertilizer_id = fertilizer_maps.id
        and fertilizer_maps.fertility_map_id =( select distinct fertility_map_id from fertilizer_maps where fertilizer_maps.layer_id=%s )
        and fertilizer_map_payments.crops_id =
        (
            select crops_id from fertilizer_map_properties where fertilizer_map_id =
            (select id from fertilizer_maps where layer_id=%s)
        )
        --same year
        and fertilizer_map_payments.download_date >= TIMESTAMP '%s'
        and fertilizer_map_payments.area > 0 ;";
        $rawQuery =sprintf($queryTemplate,$layerId,$layerId,$startDate);
        $fertilizerIds = DB::select($rawQuery);
        return $fertilizerIds;
    }

    /**
     * Get all unpaid map info selection
     * @param $fertilizerIds list of fertilizer ids that have same crops and year
     * @return list of unpaid map info
     */
    public function getListMapInfo($layerId,$fertilizerIds){
        if (!is_numeric($layerId)) {
            throw new GisException(trans('common.common_layer_id_invalid'), SystemCode::NOT_FOUND);
        }
        $query = null;
        foreach($fertilizerIds as $key){
            if($query !== null){
                $query = $query.','.$key->fertilizer_id;
            }
            else $query = $key->fertilizer_id;
        }
        $rawQuery ="";
        if($query !== null){
            $rawQuery = $rawQuery.sprintf("select
            map_info_id from fertility_map_selection_infos
            where fertility_map_selection_id =
            (select id from fertility_map_selections where fertilizer_map_id =
            (select id from fertilizer_maps where layer_id = %s))
            and map_info_id not in (select
            map_info_id from fertility_map_selection_infos
            where fertility_map_selection_id in
            (select id from fertility_map_selections where fertilizer_map_id in (%s)));",$layerId,$query);
        }
        else{
            $rawQuery = $rawQuery.sprintf("select map_info_id from fertility_map_selection_infos
                where fertility_map_selection_id =
                (select id from fertility_map_selections where
                fertilizer_map_id = (select id from fertilizer_maps where layer_id =%s
                ));",$layerId);
            ;
        }
        $listMapInfo = DB::select($rawQuery);
        return $listMapInfo;
    }
}