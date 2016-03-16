<?php
namespace Gis\Models\Repositories;

use Gis\Models\Entities\FertilityMapSelection;
use Illuminate\Support\Facades\DB;
use DateTime;
class FertilityMapSelectionRepositoryEloquent extends GisRepository implements FertilityMapSelectionRepository
{

    public $timestamps = false;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\FertilityMapSelection';
    }

    /**
     * Filter fertility map selection by conditions
     * Get the selection area of other fertilizers that have
     * -- same year
     * -- same crops
     * -- same fertility map
     * In order to display the highlight this selection area for end-user
     * And use can select the area inside that selection area to create new a fertilizer map
     * @param array() $conditions            
     *
     * @return Illuminate\Database\Eloquent\Collection $collection
     */
    public function filterByInfo($conditions)
    {
        $qb = FertilityMapSelection::Select('ins_time', 'id');
        $fertilizerIds = $this->getListOfFertilizerIds($conditions["fertility_map_id"],
            $conditions["crops_id"]);
        $query = null;
        foreach($fertilizerIds as $key){
            if($query !== null){
                $query = $query.','.$key->other_fertilizer_map_id;
            }
            else $query = $key->other_fertilizer_map_id;
        }
        $qb->whereRaw("fertilizer_map_id in (".$query.") and crops_id=".$conditions["crops_id"]);
        
        return $qb->lists('ins_time', 'id');
    }

    /**
     * Get all the fertilizer ids that have the same crops and year and fertility map
     * and that're paid already
     * @param $fertilityId the id of fertility map
     * @return mixed
     */
     function getListOfFertilizerIds($fertilityId,$cropsId){
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
         $queryTemplate = "select distinct fertilizer_map_payments.fertilizer_id as other_fertilizer_map_id,
            fertilizer_maps.fertility_map_id
                 from fertilizer_map_payments,fertilizer_maps
                where fertilizer_map_payments.fertilizer_id = fertilizer_maps.id
                --same fertility map
                and fertilizer_maps.fertility_map_id = %s
                --same crops
                and fertilizer_map_payments.crops_id = %s
                --same year
                and fertilizer_map_payments.download_date >= TIMESTAMP '%s'
                ;";
        $rawQuery = sprintf($queryTemplate,$fertilityId,$cropsId,$startDate);
        $fertilizerIds = DB::select($rawQuery);
        return $fertilizerIds;
    }
    /**
     * update fertilizer map is ready
     */
    public function updateIsReady($fertilizerId)
    {
        return $this->model->where('fertilizer_map_id', $fertilizerId)->update([
            'is_ready' => true
        ]);
    }
}