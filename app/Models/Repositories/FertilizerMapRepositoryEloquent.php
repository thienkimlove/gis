<?php

namespace Gis\Models\Repositories;

/**
 * Fertilizer repository provider functional access to database.It like same data provider layer.
 * Class FertilizerRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class FertilizerMapRepositoryEloquent extends GisRepository implements FertilizerMapRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\FertilizerMap';
    }

    /**
     * @param $layer_id
     * @return mixed
     */
    public function findByLayerId($layer_id)
    {
        return $this->findByField('layer_id',$layer_id)->first();
    }

    public function getFertilizerMapId($layer_id){
        return $this->findByField('layer_id',$layer_id)->get(['id'])->first();
    }

    public function getFertilityMapId($layer_id){
        return $this->findByField('layer_id',$layer_id)->get(['fertility_map_id'])->first();
    }
}