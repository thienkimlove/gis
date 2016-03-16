<?php

namespace Gis\Models\Repositories;

/**
 * FertilizerDetail repository provider functional access to database.It like same data provider layer.
 * Class FertilizerDetailRepositoryEloquent
 *
 * @package namespace Gis\Models\Repositories;
 */
class FertilizerDetailRepositoryEloquent extends GisRepository implements FertilizerDetailRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
    	return 'Gis\Models\Entities\FertilizerDetail';
    }
}