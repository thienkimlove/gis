<?php

namespace Gis\Models\Repositories;
use Gis\Models\Entities\FertilizerMapInfo;

/**
 * Fertilizer repository provider functional access to database.It like same data provider layer.
 * Class FertilizerRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class FertilizerMapInfoRepositoryEloquent extends GisRepository implements FertilizerMapInfoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\FertilizerMapInfo';
    }

    /**
     * Get number of mesh of fertilizer map
     * @param $fertilizer_id
     * @return mixed
     */
    public function getNumberOfMesh($fertilizer_id)
    {
        return $this->model->where('fertilizer_id','=',$fertilizer_id)->count();
    }
    public function calculateBarrel($value, $meshSize){
        $area = pow($meshSize,2)/100;
        if($area ==0){
            return 0;
        }
        return round(($value*10)/$area);
    }
}