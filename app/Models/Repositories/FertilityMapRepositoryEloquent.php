<?php
namespace Gis\Models\Repositories;

use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Gis\Models\Entities\MapInfo;

class FertilityMapRepositoryEloquent extends BaseRepository implements FertilityMapRepository
{

    public $timestamps = false;

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\FertilityMap';
    }

    /**
     * get list user with folder
     * update this when using
     *
     * @return array
     */
    public function getFertilityMapWithNoFolderLayer()
    {
        return FertilityMapFacade::all();
    }

    /**
     * Find fertility map by Id
     *
     * @param int $id            
     *
     * @return Gis\Models\Entities\FertilityMap $fertilityMap
     */
    public function findById($id)
    {
        return $this->findByField('id', $id)->first();
    }

    /**
     * Load Map Info by fertility map id
     *
     * @param int $fertilityMapId            
     *
     * @return Gis\Models\Entities\MapInfo $mapInfo
     */
    public function loadMapInfoByFertilityMapId($fertilityMapId)
    {
        return MapInfo::where('fertility_id', $fertilityMapId)->limit(1);
    }

    /**
     * Get List Nitrogen By map info ids
     *
     * @param unknown $mapInfoIds            
     *
     * @return array() $listNitroGens
     */
    public function getListNitrogensByIds($mapInfoIds)
    {
        $mapInfos = MapInfo::Select()->whereIn('id', $mapInfoIds)->get();
        $listNitroGens = array();
        if (! $mapInfos->isEmpty()) {
            foreach ($mapInfos as $mapInfo) {
                array_push($listNitroGens, $mapInfo->nitrogen);
            }
        }
        return array_unique($listNitroGens);
    }

    /**
     * Get list nitrogen by fertility map id
     * @param $fertilityMapId
     */

    public function getListNitrogenByFertilityMapId($fertilityMapId){
       $mapInfos = MapInfo::select("nitrogen")->where('fertility_id',"=", $fertilityMapId)
            ->distinct()->orderBy('nitrogen', 'ASC')->get();
        $listNitroGens = array();
        if (! $mapInfos->isEmpty()) {
            foreach ($mapInfos as $mapInfo) {
                array_push($listNitroGens, $mapInfo->nitrogen);
            }
        }
        return array_unique($listNitroGens);
    }
}