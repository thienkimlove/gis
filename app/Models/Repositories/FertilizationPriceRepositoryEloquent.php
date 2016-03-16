<?php

namespace Gis\Models\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use Gis\Models\Entities\FertilizationPrice;

/**
 * Class FertilizationPriceRepositoryEloquent
 * @package namespace Gis\Repositories;
 */
class FertilizationPriceRepositoryEloquent extends GisRepository implements FertilizationPriceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return 'Gis\Models\Entities\FertilizationPrice';
    }

    public function getAllDate($id){
        return $this->model->where('id','!=', $id)->get();
    }
    public function checkExistence($id){
        return $this->model->where('id','=', $id)->get();
    }
}