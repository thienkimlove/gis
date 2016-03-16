<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Gis\Models\Entities\FertilizerMapProperty;

/**
 * Class FertilizerMapPropertyRepositoryEloquent
 * @package namespace Gis\Repositories;
 */
class FertilizerMapPropertyRepositoryEloquent extends GisRepository implements FertilizerMapPropertyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\FertilizerMapProperty';
    }

    public function getCropID($fertilizer_map_id){
        return $this->model->where('fertilizer_map_id', '=',$fertilizer_map_id)->get(['crops_id'])->first();
    }
}