<?php

namespace Gis\Models\Repositories;

/**
 * Fertilizer repository provider functional access to database.It like same data provider layer.
 * Class FertilizerRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class CropRepositoryEloquent extends GisRepository implements CropRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\Crop';
    }

    /**
     * Get the crop name by crop id
     * @param $cropID
     * @return mixed
     */
    public function getCropName($cropID){
        return $this->model->where('id', '=', (int)$cropID)->get(['crops_name'])->first();
    }
}